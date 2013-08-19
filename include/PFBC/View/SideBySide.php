<?php
namespace PFBC\View;

class SideBySide extends \PFBC\View {
	protected $class = "form-horizontal";

	public function render() {
		$this->_form->appendAttribute("class", $this->class);
        $labels=$this->classLabels;

        if(isset($labels["fieldsetLabel"])){
            echo '<form', $this->_form->getAttributes(), '><fieldset class="'.$labels["fieldsetLabel"].'">';
        }
        else
            echo '<form', $this->_form->getAttributes(), '><fieldset>';
		$this->_form->getErrorView()->render();

        $elements = $this->_form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;
        for($e = 0; $e < $elementSize; ++$e) {
            $element = $elements[$e];

            if($element instanceof \PFBC\Element\Hidden || $element instanceof \PFBC\Element\HTML)
                $element->render();
            elseif($element instanceof \PFBC\Element\Button) {
                if($e == 0 || !$elements[($e - 1)] instanceof \PFBC\Element\Button){
                    if(isset($labels["formAction"])){
                        echo '<div class="'.$labels["formAction"].'">';
                    }
                    else
                        echo '<div class="form-actions">';
                }
                else
                    echo ' ';

                $element->render();

                if(($e + 1) == $elementSize || !$elements[($e + 1)] instanceof \PFBC\Element\Button)
                    echo '</div>';
            }
            else {
                if(isset($labels["controlGroup"])){
                    echo '<div class="'.$labels["controlGroup"].'">', $this->renderLabel($element), '<div class="controls">', $element->render(), $this->renderDescriptions($element), '</div></div>';
                }
                else
                    echo '<div class="control-group">', $this->renderLabel($element), '<div class="controls">', $element->render(), $this->renderDescriptions($element), '</div></div>';
                ++$elementCount;

            }
        }

        echo '</fieldset></form>';
    }

    protected function renderLabel(\PFBC\Element $element) {
        $label = $element->getLabel();
        if(!empty($label)) {
            if(isset($labels["controlLabel"])){
                echo '<label class="'.$labels["controlLabel"].'" for="', $element->getAttribute("id"), '">';
            }
            else
                echo '<label class="control-label" for="', $element->getAttribute("id"), '">';
            if($element->isRequired())
                echo '<span class="required">* </span>';
            echo $label, '</label>';
        }
    }
}
