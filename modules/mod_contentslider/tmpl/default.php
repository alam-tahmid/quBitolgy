<?php
/*------------------------------------------------------------------------
# Content Slider - Version 1.0
# Copyright (C) 2009-2010 YouTechClub.Com. All Rights Reserved.
# @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Author: YouTechClub.Com
# Websites: http://www.youtechclub.com
-------------------------------------------------------------------------*/

?>

<?php if (sizeof($items) > 0) :?>
<script language="javascript">
	JTT(document).ready(function($){
		$('#slider<?php echo $module->id?>').nivoSlider({
			effect:'<?php echo $effect ?>',
			slices: <?php echo $slices ?>,
			animSpeed: <?php echo $animSpeed ?>,
			pauseTime: <?php echo $pauseTime ?>,
			startSlide: <?php echo $startSlide ?>,			
			directionNav: <?php echo $directionNav ?>,
			directionNavHide: <?php echo $directionNavHide ?>,
			controlNav: <?php echo $controlNav ?>,
			controlNavThumbs: <?php echo $controlNavThumbs ?>,
			controlNavThumbsFromRel:false,
			controlNavThumbsSearch: '<?php echo $controlNavThumbsSearch ?>',
			controlNavThumbsReplace: '<?php echo $controlNavThumbsReplace ?>',
			keyboardNav: <?php echo $keyboardNav ?>,
			pauseOnHover: <?php echo $pauseOnHover ?>,
			manualAdvance: <?php echo $manualAdvance ?>,
			captionOpacity: <?php echo $captionOpacity ?>
		});
		<?php if (!$auto_play) :?>
			$('#slider<?php echo $module->id?>').data('nivo:vars').stop = true; //Stop the Slider
		<?php endif;?>
	});
	
</script>
<?php if ($controlNavThumbs) :?>
<style type="text/css">
#slider<?php echo $module->id?> {
	margin-bottom:110px;
}
#slider<?php echo $module->id?> .nivo-controlNav {
	position:absolute;
	left:185px;
	bottom:-70px;
}
#slider<?php echo $module->id?> .nivo-controlNav a {
    display:inline;
}
#slider<?php echo $module->id?> .nivo-controlNav img {
	display:inline;
	position:relative;
	margin-right:10px;
	-moz-box-shadow:0px 0px 5px #333;
	-webkit-box-shadow:0px 0px 5px #333;
	box-shadow:0px 0px 5px #333;
}
#slider<?php echo $module->id?> .nivo-controlNav a.active img {
    border:1px solid #000;
}
</style>
<?php else : ?>
<style type="text/css">
#slider<?php echo $module->id?> .nivo-controlNav a {
    text-indent:-9999px;
}
#slider<?php echo $module->id?> .nivo-controlNav {
	position:absolute;
	left:260px;
	bottom:-42px;
}
#slider<?php echo $module->id?> .nivo-controlNav a {
	display:block;
	width:22px;
	height:22px;
	background:url(<?php echo JURI::base()?>modules/mod_contentslider/assets/bullets.png) no-repeat;
	text-indent:-9999px;
	border:0;
	margin-right:3px;
	float:left;
}
#slider<?php echo $module->id?> .nivo-controlNav a.active {
	background-position:0 -22px;
}

</style>
<?php endif;?>
<div id="slider-wrapper" style="width: <?php echo $thumb_width?>px; height:<?php echo ($thumb_height + 146)?>px">
	
<div class="nivoSlider" id="slider<?php echo $module->id?>" style="width: <?php echo $thumb_width?>px; height:<?php echo $thumb_height?>px"> 	
	
	<?php foreach($items as $key => $item):?>		
		<a href="<?php echo $item['link']?>"><img src="<?php echo $item['thumb']?>" alt="<?php echo $item['title']?>" title="<?php echo $show_title ? ('#htmlcaption'. $module->id . '_' . $key) : '' ?>"/></a>
	<?php endforeach;?>			
	
		
</div>
	<?php
	if ($show_title) :
	foreach($items as $key => $item):?>	
	<div id="htmlcaption<?php echo  $module->id. '_' . $key;?>" class="nivo-html-caption">
		<h3><a href="<?php echo $item['link']?>"><?php echo $item['title']?></a></h3>
	</div>		
	<?php endforeach;?>	
	<?php endif;?>
</div>
<?php endif;?>