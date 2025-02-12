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

use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ILIAS\UI\Renderer;
use ILIAS\UI\Component\Component;
use LogicException;

class PluginRenderer extends AbstractComponentRenderer
{
    protected function getComponentInterfaceName(): array
    {
        return [SelectForm::class];
    }

    public function render(Component $component, Renderer $default_renderer): string
    {
        $this->checkComponent($component);
        switch (true) {
            case ($component instanceof SelectForm):
                return $this->renderSelectForm($component, $default_renderer);
            default:
                throw new LogicException("Cannot render '" . get_class($component) . "'");
        }
    }

    /**
     * Render a SelectForm component
     * The form is automatically submitted when its signal is triggered
     * @see \ILIAS\UI\Implementation\Component\Input\Container\Form\Renderer::renderNoSubmit
     */
    private function renderSelectForm(SelectForm $component, Renderer $default_renderer): string
    {
        $form_id = $this->createId();
        $tpl = $this->getTemplate("tpl.select_form.html", true, true);

        foreach ($component->getIndexedItems() as $item_id => $item_text) {
            $tpl->setCurrentBlock('item');
            $tpl->setVariable('FORM_ID', $form_id);
            $tpl->setVariable('ITEM_ID', $item_id);
            $tpl->setVariable('ITEM_TEXT', $item_text);
            $tpl->setVariable('POST_VAR', $component->getPostVar());
            $tpl->parseCurrentBlock();
        }

        $tpl->setVariable("ID", $form_id);
        $tpl->setVariable("SUBMIT_SIGNAL", $component->getSubmitSignal()->getId());
        $tpl->setVariable('POST_URL', $component->getPostUrl());
        $tpl->setVariable('POST_VAR', $component->getPostVar());
        ;

        return $tpl->get();
    }

    protected function getTemplatePath($name): string
    {
        return __DIR__ . '/../tpl/' . $name;
    }
}
