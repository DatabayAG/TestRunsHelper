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

use ILIAS\UI\Component\JavaScriptBindable;
use ILIAS\UI\Component\Input\Container\Form\FormWithPostURL;
use ILIAS\UI\Implementation\Component\JavaScriptBindable as JavaScriptBindableTrait;
use ILIAS\UI\Implementation\Component\Input\Container\Form\HasPostURL;
use ILIAS\UI\Implementation\Component\SignalGeneratorInterface;
use ILIAS\UI\Component\Signal;

class SelectForm implements JavaScriptBindable
{
    use JavaScriptBindableTrait;

    protected Signal $submit_signal;

    /** @var array<int, string> */
    protected array $indexed_items;
    protected string $post_var;
    protected string $post_url;

    public function __construct(
        SignalGeneratorInterface $signal_generator,
        array $indexed_items,
        string $post_var,
        string $post_url
    ) {
        $this->submit_signal = $signal_generator->create();
        $this->indexed_items = $indexed_items;
        $this->post_var = $post_var;
        $this->post_url = $post_url;
    }

    public function getCanonicalName(): string
    {
        return 'SelectForm';
    }

    public function getIndexedItems(): array
    {
        return $this->indexed_items;
    }

    public function getSubmitSignal()
    {
        return $this->submit_signal;
    }

    public function getPostVar(): string
    {
        return $this->post_var;
    }

    public function getPostUrl(): string
    {
        return $this->post_url;
    }
}
