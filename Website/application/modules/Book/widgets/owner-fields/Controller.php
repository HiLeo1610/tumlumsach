<?php
class Book_Widget_OwnerFieldsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
	$user = $subject->getOwner();
	if (!$user)
	{
		return $this->setNoRender();
	}

    // Get subject and check auth
    if( !$user->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }


    // Load fields view helpers
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    // Values
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($user);
    if( count($fieldStructure) <= 1 ) { // @todo figure out right logic
      return $this->setNoRender();
    }
    return;

    $valuesStructure = array();
    $valueCount = 0;
    foreach( $fieldStructure as $index => $field )
    {
      $value = $field->getValue($subject);
      if( !$field->display )
      {
        continue;
      }

      if( $field->isHeading() )
      {
        $valuesStructure[] = array(
          'alias' => null,
          'label' => $field->label,
          'value' => $field->label,
          'heading' => true,
          'type' => $field->type,
        );
      }

      else if( $value && !empty($value->value) )
      {
        $valueCount++;

        $label = Engine_Api::_()->fields()
                 ->getFieldsOptions($user)
                 ->getRowMatching('option_id', $value->value);
        $label = $label
                 ? $label->label
                 : $value->value;

        $valuesStructure[] = array(
          'alias' => $field->alias,
          'label' => $field->label,
          'value' => $label,
          'heading' => false,
          'type' => $field->type,
        );
      }
    }
    $this->view->user   = $user;
    $this->view->fields = $valuesStructure;
    $this->view->valueCount = $valueCount;


    // Do not render if nothing to show
    if( $valueCount <= 0 ) {
      return $this->setNoRender();
    }
  }
}