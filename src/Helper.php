<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *********************************************************************/

declare(strict_types=1);

namespace ILIAS\Plugin\TestRunsHelper;

use ilObjTest;
use ilDBInterface;
use ilTestParticipantList;
use ilStr;
use ilDBConstants;
use ilTestParticipant;

class Helper
{
    private ilObjTest $test;
    private ilDBInterface $db;
    private ilTestParticipantList $participants_list;

    public function __construct(
        ilObjTest $test,
        ilDBInterface $db
    ) {
        $this->test = $test;
        $this->db = $db;
    }

    /**
     * Check of the test has settings that allow a continuation of finished passes
     */
    public function canPassesBeContinued(): bool
    {
        return $this->test->getNrOfTries() == 1 && $this->test->getEnableProcessingTime();
    }

    /**
     * Check if participants with unfinished passes exist
     */
    public function hasFinishedPasses(): bool
    {
        foreach ($this->getParticipantsList() as $participant) {
            if (!$participant->hasUnfinishedPasses()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get a list of finished participants
     * @return array<ilTestParticipant> names for display, indexed by active_id
     */
    public function getFinishedParticipants(): array
    {
        $list = $this->getParticipantsList();

        $finished = [];
        foreach ($list as $p) {
            if ($p->getActiveId() !== null && !$p->hasUnfinishedPasses()) {
                $finished[] = $p;
            }
        }
        uasort($finished, fn(ilTestParticipant $a, ilTestParticipant $b) => ilStr::strCmp($list->buildFullname($a), $list->buildFullname($b)));

        $names = [];
        foreach ($finished as $p) {
            $names[$p->getActiveId()] = $list->buildFullname($p) . (empty($p->getLogin()) ? '' : ' [' . $p->getLogin() . ']');
        }

        return $names;
    }

    /**
     * Continue the test passes of participants given by active ids
     * @param int[] $active_ids
     * @return int the number of affected participants
     */
    public function continuePasses(array $active_ids): int
    {
        $query = "
            UPDATE tst_active
            SET tries = 0, submitted = 0, submittimestamp = NULL, last_finished_pass = NULL
            WHERE test_fi = " . $this->db->quote($this->test->getTestId()) . "
            AND " . $this->db->in('active_id', $active_ids, false, ilDBConstants::T_INTEGER);

        return $this->db->manipulate($query);
    }

    /**
     * Get a cached participants list of the test
     */
    private function getParticipantsList(): ilTestParticipantList
    {
        if (!isset($this->participants_list)) {
            $this->participants_list = $this->test->getActiveParticipantList();
        }
        return $this->participants_list;
    }
}
