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

use ILIAS\UI\Factory;
use ILIAS\UI\Renderer;
use ILIAS\HTTP\Wrapper\RequestWrapper;
use ILIAS\Plugin\TestRunsHelper\Helper;
use ILIAS\Plugin\TestRunsHelper\SelectForm;
use ILIAS\UI\Implementation\Component\SignalGeneratorInterface;
use ILIAS\Plugin\TestRunsHelper\SelectFormRenderer;
use ILIAS\Plugin\TestRunsHelper\PluginRenderer;

/**
 * @ilCtrl_IsCalledBy ilTestRunsHelperGUI: ilUIPluginRouterGUI
 */
class ilTestRunsHelperGUI
{
    private ilCtrl $ctrl;
    private ilAccessHandler $access;
    private ilGlobalTemplateInterface $tpl;
    private ilLanguage $lng;
    private ilToolbarGUI $toolbar;
    private Factory $ui_factory;
    private Renderer $ui_renderer;
    private ilPlugin $plugin;
    private RequestWrapper $query;
    private RequestWrapper $post;
    private ILIAS\Refinery\Factory $refinery;
    private SignalGeneratorInterface $signal_generator;

    private int $ref_id;
    private ilObjTest $test;
    private Helper $helper;
    private PluginRenderer $plugin_renderer;

    public function __construct()
    {
        global $DIC;

        $this->ctrl = $DIC->ctrl();
        $this->access = $DIC->access();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->toolbar = $DIC->toolbar();
        $this->lng = $DIC->language();
        $this->ui_factory = $DIC->ui()->factory();
        $this->ui_renderer = $DIC->ui()->renderer();
        $this->query = $DIC->http()->wrapper()->query();
        $this->post = $DIC->http()->wrapper()->post();
        $this->refinery = $DIC->refinery();
        $this->signal_generator = $DIC["ui.signal_generator"];

        $this->lng->loadLanguageModule('assessment');

        /** @var ilComponentFactory $factory */
        $factory = $DIC["component.factory"];
        $this->plugin = $factory->getPlugin('teruhe');

        $this->ref_id = $this->query->retrieve('ref_id', $this->refinery->kindlyTo()->int());

        $this->test = new ilObjTest($this->ref_id);
        $this->helper = new Helper($this->test, $DIC->database());

        $this->plugin_renderer = new PluginRenderer(
            $DIC["ui.factory"],
            $DIC["xlas.custom_template_factory"],
            $DIC["lng"],
            $DIC["ui.javascript_binding"],
            $DIC["refinery"],
            $DIC["ui.pathresolver"],
            $DIC["ui.data_factory"]
        );
    }


    public function executeCommand()
    {
    }


    public function modifyToolbar()
    {
        if ($this->access->checkAccess('write', '', $this->ref_id)
            && $this->helper->canPassesBeContinued()
            && $this->helper->hasFinishedPasses()
        ) {
            $form = new SelectForm(
                $this->signal_generator,
                $this->helper->getFinishedParticipants(),
                'active_id',
                $this->ctrl->getLinkTargetByClass(['ilUIPluginRouterGUI', 'ilTestRunsHelperGUI'], 'reopenPasses')
            );
            $rendered_form = $this->ui_factory->legacy($this->plugin_renderer->render($form, $this->ui_renderer));

            $modal = $this->ui_factory->modal()->roundtrip($this->plugin->txt('reopen_passes'), $rendered_form)
                ->withActionButtons([
                    $this->ui_factory->button()->standard($this->plugin->txt('reopen_passes'), '#')
                        ->withOnClick($form->getSubmitSignal())
                ]);

            $button = $this->ui_factory->button()->standard($this->plugin->txt('reopen_passes'), '#')
                ->withOnClick($modal->getShowSignal());

            $this->tpl->addLightbox($this->ui_renderer->render($modal), 'iltestrunshelpermodal');
            $this->toolbar->addComponent($button);
        }
    }

    public function reopenPasses()
    {

    }
}
