<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<!-- standard layout -->
<!-- Place this tag where you want the widget to render. -->
<div class="g-page" 
	data-href="<?php echo $this->config['gplus_user']; ?>" 
	data-rel="publisher" 
	data-layout="<?php echo $this->config['gplus_badge_layout']; ?>" 
	data-theme="<?php echo $this->config['gplus_badge_color']; ?>" 
	data-showtagline="<?php echo $this->config['gplus_tagline']; ?>" 
	data-showcoverphoto="<?php echo $this->config['gplus_cover_photo']; ?>" 
	data-width="<?php echo $this->config['gplus_badge_width']; ?>">
</div>