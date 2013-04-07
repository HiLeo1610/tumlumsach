<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<ul>
  <?php if( !empty($this->memberType) ): ?>
  <li>
    <?php echo $this->translate('Member Type:') ?>
    <?php // @todo implement link ?>
    <?php echo $this->translate($this->memberType) ?>
  </li>
  <?php endif; ?>
  <?php if( !empty($this->networks) && count($this->networks) > 0 ): ?>
  <li>
    <?php echo $this->translate('Networks:') ?>
    <?php echo $this->fluentList($this->networks) ?>
  </li>
  <?php endif; ?>
  <li>
    <?php echo $this->translate('Profile Views:') ?>
    <?php echo $this->translate(array('%s view', '%s views', $this->user->view_count),
        $this->locale()->toNumber($this->user->view_count)) ?>
  </li>
  <li>
    <?php $direction = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction');
    if ( $direction == 0 ): ?>
      <?php echo $this->translate('Followers:') ?>  
      <?php echo $this->translate(array('%s follower', '%s followers', $this->user->member_count),
        $this->locale()->toNumber($this->user->member_count)) ?>      
    <?php else: ?>  
    <?php echo $this->translate('Friends:') ?>
    <?php echo $this->translate(array('%s friend', '%s friends', $this->user->member_count),
        $this->locale()->toNumber($this->user->member_count)) ?>
    <?php endif; ?>
  </li>
  <li>
    <?php echo $this->translate('Last Update:'); ?>
    <?php echo $this->timestamp($this->user->modified_date) ?>
  </li>
  <li>
    <?php echo $this->translate('Joined:') ?>
    <?php echo $this->timestamp($this->user->creation_date) ?>
  </li>
  <?php if( !$this->user->enabled && $this->viewer->isAdmin() ): ?>
  <li>
    <em>
      <?php echo $this->translate('Enabled:') ?>
      <?php echo $this->translate('No') ?>
    </em>
  </li>
  <?php endif; ?>
</ul>