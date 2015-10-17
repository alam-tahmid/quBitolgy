<?php
/*------------------------------------------------------------------------
# TZ Content Slider
# ------------------------------------------------------------------------
# Author    ThemeZart http://www.ThemeZart.com
# Copyright (C) 2010 - 2012 ThemeZart.com. All Rights Reserved.
# @license - GNU/GPL V2 for PHP files. CSS / JS are Copyrighted Commercial
# Websites: http://www.ThemeZart.com
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$modId = $module->id;
if ($article_column>=$c_article_count):
	$article_column 	= $c_article_count;
	$article_row		= 1;
endif;
$date_time 				= '';
?><style type="text/css">#tzcontentslider-<?php echo $modId; ?> div.tzcontentslider-art-pages,#tzcontentslider-<?php echo $modId; ?> div.tzcontentslider-page {	height: <?php echo $min_module_height; ?>px !important;}</style>
<div id="tzcontentslider-<?php echo $modId; ?>" class="tzslider tzcontentslider-<?php echo $uniqid ?>">
	<div class="tzcontentslider-wrap">
		<?php if ($c_article_count > 0): ?>
			<div class="tzcontentslider-art-wrap <?php if ($links_block && $c_links_count!=0 && $links_position=="right"): ?> col-2 flt-left<?php endif; ?>">
				<div class="tzcontentslider-art-pages">
				<?php for($i=0;$i<$c_article_count;$i++): ?>
					<div class="tzcontentslider-page">
						<div class="tzcontentslider-page-inner">
						<?php for($j=0;$j<$article_row;$j++, $i++): ?>
							<div class="tzcontentslider-row <?php echo $j==0 ? 'tzcontentslider-first' : '' ?> <?php echo $j%2 ? 'tzcontentslider-even' : 'tzcontentslider-odd' ?>">
								<div class="tzcontentslider-row-inner">
								<?php for($z=0;$z<$article_column;$z++, $i++): ?>
									<?php if ($i <$c_article_count): ?>
									<div class="tzcontentslider-column flt-left col-<?php echo $article_column ?>">
										<div style="padding:<?php echo $article_col_padding ?>">
											<div class="tzcontentslider-inner">
												<?php /*Date type blog*/ if ($article_date_format=='blog'): ?>
													<div class="tzcontentslider-date-blog">
														<?php
															$date_time = explode(' ', JHTML::_('date', $list[$i]->created, JText::_('DATE_FORMAT_LC3')));
															echo '<span class="tzcontentslider_date_day">' . JText::_(strtoupper($date_time[0])) . '</span>';
															echo '<div class="tzcontentslider_date_month_year"><span class="tzcontentslider_date_month">' . $date_time[1] . '</span><span class="tzcontentslider_date_year">' . $date_time[2] . '</span></div>';
														?>
													</div>
												<?php endif; ?>												
											
												<?php /*Image position before title*/if ($article_show_image && $article_image_pos=='top' && $list[$i]->image): ?>
													<?php if ($article_linked_image): ?>
														<a href="<?php echo $list[$i]->link ?>">
													<?php endif; ?>	
														<img class="tzcontentslider-image" style="<?php echo $article_image_float ?>;<?php echo ($article_image_margin) ? "margin:$article_image_margin" : "" ?>" src="<?php echo modTzContentSliderCommonHelper::thumb($list[$i]->image, $article_thumb_width, $article_thumb_height, $article_thumb_ratio, $uniqid) ?>" width="<?php echo $article_thumb_width ?>" height="<?php echo $article_thumb_height ?>" alt="<?php echo $list[$i]->title ?>" title="<?php echo $list[$i]->title ?>" />
													<?php if ($article_linked_image): ?>		
														</a>
													<?php endif; ?>			
												<?php endif; ?>												
												
												<?php /*Article title*/ if ($article_showtitle): ?>
													<h4 class="tzcontentslider-title">
														<?php if ($article_linkedtitle): ?>
															<a href="<?php echo $list[$i]->link ?>">
														<?php endif; ?>	
															<?php echo modTzContentSliderCommonHelper::cText($params,$list[$i]->title, $article_count_title_text, $article_title_text_limit); ?>
														<?php if ($article_linkedtitle): ?>
															</a>
														<?php endif; ?>	
													</h4>
												<?php endif; ?>
												
												<?php /*Author, Category, date*/ if ($article_show_author || $article_date_format || $article_show_category): ?>
													<div class="tzcontentslider-tools">
														<?php /*Show Author*/ if ($article_show_author): ?>
															<div class="tzcontentslider-author">
																<? echo '<span>' . JText::_('MODTZ_SLIDER_WRITTEN') . '</span>' . $list[$i]->author; ?>
															</div>
														<?php endif; ?>

														<?php /*Show category*/ if ($article_show_category): ?>
															<div class="tzcontentslider-category">
																<?php if ($article_show_author): ?>
																<span><?php echo JText::_('MODTZ_SLIDER_CATEGORY'); ?></span>
																<?php endif; ?>	
																<?php if ($article_linked_category): ?>
																	<a href="<?php echo $list[$i]->cat_link ?>">
																<?php endif; ?>	
																	<?php echo $list[$i]->category ?>
																<?php if ($article_linked_category): ?>
																	</a>
																<?php endif; ?>	
															</div>														
														<?php endif; ?>													
														
														<?php /*Show date*/ if (($article_date_format) && ($article_date_format!='blog')): ?>
															<div class="tzcontentslider-created">
																<?php if ($article_show_author || $article_show_category): ?>
																	<span><?php echo JText::_('MODTZ_SLIDER_CREATED') ?></span>
																<?php endif; ?>
																<?php echo JHTML::_('date', $list[$i]->created, JText::_($article_date_format)) ?>
															</div>
														<?php endif; ?>
													</div>
												<?php endif; ?>
	
												<?php /*Image position after title*/if ($article_show_image && $article_image_pos=='bottom' && $list[$i]->image): ?>
													<?php if ($article_linked_image): ?>
														<a href="<?php echo $list[$i]->link ?>">
													<?php endif; ?>	
														<img class="tzcontentslider-image" style="<?php echo $article_image_float ?>;<?php echo ($article_image_margin) ? "margin:$article_image_margin" : "" ?>" src="<?php echo modTzContentSliderCommonHelper::thumb($list[$i]->image, $article_thumb_width, $article_thumb_height, $article_thumb_ratio, $uniqid) ?>" width="<?php echo $article_thumb_width ?>" height="<?php echo $article_thumb_height ?>" alt="<?php echo $list[$i]->title ?>" title="<?php echo $list[$i]->title ?>" />
													<?php if ($article_linked_image): ?>		
														</a>
													<?php endif; ?>			
												<?php endif; ?>			
												
												<?php /*Ratings*/ if ($article_show_ratings): ?>
													<div class="tzcontentslider-rating">
														<div class="tzcontentslider-rating-bar">
															<div style="width:<?php echo $list[$i]->rating ?>%"></div>	
														</div>	
													</div>
												<?php endif; ?>

												<?php /*Introtext*/ if ($article_introtext): ?>
													<div class="tzcontentslider-introtext">
														<?php echo modTzContentSliderCommonHelper::cText($params,$list[$i]->introtext, $article_count_intro_text, $article_intro_text_limit); ?>
													</div>
												<?php endif; ?>
												
												<?php /*Virtuemart*/ if ($art_show_price || $art_show_cart_button) : ?>
													<div class="tzcontentslider-vm-bar">
														<?php /*Show Price*/ if ($art_show_price) : ?>
															<p class="tzcontentslider-vm-price"><?php echo $list[$i]->price ?></p>
														<?php endif; ?>

														<?php /*Show Cart Button*/ if ($art_show_cart_button) : ?>
															<?php echo $list[$i]->addtocart ?>
														<?php endif; ?>
													</div>
												<?php endif; ?>
												
												
												<?php /*K2 Extra fields*/ if ($article_extra_fields && $content_source == 'k2' && count($list[$i]->extra_fields)): ?>
													<div style="clear:both"></div>
													<div class="TZ_SLIDERK2ExtraFields">
														<b><?php echo JText::_('Additional Info'); ?></b>
														<ul>
															<?php foreach ($list[$i]->extra_fields as $key=>$extraField): ?>
																<li class="type<?php echo ucfirst($extraField->type); ?> group<?php echo $extraField->group; ?> <?php echo strtolower($extraField->name) ?> <?php echo $key%2 ? 'even' : 'odd';?> <?php if ($key==0) echo 'first'; ?>">
																	<span class="label"><?php echo $extraField->name; ?></span>
																	<span class="value"><?php echo $extraField->value; ?></span>
																	<div style="clear:both"></div>
																</li>
															<?php endforeach; ?>
														</ul>
													</div>
													<div style="clear:both"></div>
												<?php endif; ?>								
												
												<?php /*Comments, readmore, hits*/ if ($article_show_more || $article_hits || $article_comments): ?>
													<div class="tzcontentslider-links">
														<?php /*Comments*/ if ($article_comments):
															echo $list[$i]->comment;
														endif; ?>							
														<?php /*Hits*/ if ($article_hits): ?>
															<span class="tzcontentslider-hits"><?php echo JText::_('HITS_TEXT') . ':' . $list[$i]->hits ?></span>
														<?php endif; ?>

														<?php /*Readmore*/ if ($article_show_more): ?>
															<a class="tzcontentslider-readmore" href="<?php echo $list[$i]->link ?>"><span><?php echo $article_more_text ?></span></a>
														<?php endif; ?>
													</div>
												<?php endif; ?>
												<div style="clear:both"></div>
												
											</div>
										</div>
									</div>
									<?php endif; ?>
								<?php endfor; $i--; ?>
								<div style="clear:both"></div>
							</div>
							<div style="clear:both"></div>
							</div>
						<?php endfor; $i--; ?>
						<div style="clear:both"></div>
						</div><!--end tzcontentslider-page-inner-->
					</div>
				<?php endfor; ?>
				</div>
				
				<?php /*Navigation*/ if ($article_animation!="disabled"): ?>
					<div style="clear:both"></div>
					<div class="tzcontentslider-art-controllers">
						<?php /*Pagination*/ if ($article_pagination): ?>
							<div class="tzcontentslider-art-pagination"></div>
						<?php endif; ?>
						<?php /*Previous*/ if ($article_arrows): ?>
							<div class="tzcontentslider-art-prev">&laquo;</div>
						<?php endif; ?>
						<?php /*Play & Pause*/ if ($article_play_button): ?>
							<div class="tzcontentslider-art-play">&rsaquo;</div>
							<div class="tzcontentslider-art-pause">||</div>
						<?php endif; ?>	
						<?php /*Next*/ if ($article_arrows): ?>					
							<div class="tzcontentslider-art-next">&raquo;</div>
						<?php endif; ?>
						<div style="clear:both"></div>
					</div>
				<?php endif; ?>
				<div style="clear:both"></div>
			</div>
		<?php endif; ?>
		<!--End article layout-->
		
		<!--Links Layout-->
		<?php if ($links_block && $c_links_count!=0): ?>
		<?php 
			$links=$c_article_count;
		?>
		<div class="tzcontentslider-links-wrap <?php if ($links_position=="right"): ?> col-2 flt-left<?php endif; ?>">
			<?php if ($links_more): ?>
				<strong><?php echo  JText::_($links_more_text) ?></strong>
			<?php endif; ?>
			<div class="tzcontentslider-links-pages">
			<?php for($i=$links;$i<$links+$c_links_count;$i++): ?>	
				<div class="tzcontentslider-page">
					<div class="tzcontentslider-page-inner">
						<?php for ($ii=0; $ii<$links_count; $ii++, $i++): ?>
							<?php if ($i<$a_count): ?>
								<div class="tzcontentslider-row <?php echo $ii==0 ? 'tzcontentslider-first' : '' ?> <?php echo $ii%2 ? 'tzcontentslider-even' : 'tzcontentslider-odd' ?>">
									<div class="tzcontentslider-row-inner">
										<div style="padding:<?php echo $links_col_padding ?>">
											<div class="tzcontentslider-inner">
												<?php /*Show Image*/ if ($links_show_image && $links_image_pos=='top' && $list[$i]->image): ?>
													<?php if ($links_linked_image): ?>
														<a href="<?php echo $list[$i]->link ?>">
													<?php endif; ?>	
														<img class="tzcontentslider-image" style="<?php echo $links_image_float ?>;<?php echo ($links_image_margin) ? "margin:$links_image_margin" : "" ?>" src="<?php echo modTzContentSliderCommonHelper::thumb($list[$i]->image, $links_thumb_width, $links_thumb_height, $links_thumb_ratio, $uniqid) ?>" width="<?php echo $links_thumb_width ?>" height="<?php echo $links_thumb_height ?>" alt="<?php echo $list[$i]->title ?>" title="<?php echo $list[$i]->title ?>" />
													<?php if ($links_linked_image): ?>		
														</a>
													<?php endif; ?>	
												<?php endif; ?>													
												
												<!--Start title-->											
												<h4 class="tzcontentslider-title">
													<a href="<?php echo $list[$i]->link ?>"><?php echo modTzContentSliderCommonHelper::cText($params,$list[$i]->title, $links_title_count, $links_title_text_limit); ?></a>
												</h4>

												<?php /*Image after title*/ if ($links_show_image && $links_image_pos=='bottom' && $list[$i]->image): ?>
													<?php if ($links_linked_image): ?>
														<a href="<?php echo $list[$i]->link ?>">
													<?php endif; ?>	
														<img class="tzcontentslider-image" style="<?php echo $links_image_float ?>;<?php echo ($links_image_margin) ? "margin:$links_image_margin" : "" ?>" src="<?php echo modTzContentSliderCommonHelper::thumb($list[$i]->image, $links_thumb_width, $links_thumb_height, $links_thumb_ratio, $uniqid) ?>" width="<?php echo $links_thumb_width ?>" height="<?php echo $links_thumb_height ?>" alt="<?php echo $list[$i]->title ?>" title="<?php echo $list[$i]->title ?>" />
													<?php if ($links_linked_image): ?>		
														</a>
													<?php endif; ?>	
												<?php endif; ?>	
												
												<?php /*Intro Text*/ if ($links_show_intro): ?>
													<p class="tzcontentslider-introtext"><?php echo modTzContentSliderCommonHelper::cText($params,$list[$i]->introtext, $links_intro_count, $links_intro_text_limit) ?></p>															
												<?php endif; ?>
							
												<?php /*Virtuemart*/ if ($links_show_price || $links_show_cart_button) : ?>
													<div class="tzcontentslider-vm-bar">
														<?php /*Show Price*/ if ($links_show_price) : ?>
															<p class="tzcontentslider-vm-price"><?php echo $list[$i]->price ?></p>
														<?php endif; ?>

														<?php /*Show Cart Button*/ if ($links_show_cart_button) : ?>
															<?php echo $list[$i]->addtocart ?>
														<?php endif; ?>
													</div>
												<?php endif; ?>
												<div style="clear:both"></div>
											</div>
										</div>
										<div style="clear:both"></div>
									</div>
								</div>
							<?php endif; ?>
						<?php endfor; $i--; ?>
						<div style="clear:both"></div>
					</div><!--End tzcontentslider-page-inner-->
				</div>
			<?php endfor; ?>
			</div>
			
			<?php /*Navigation*/ if ($links_animation!="disabled"): ?>
				<div style="clear:both"></div>
				<div class="tzcontentslider-links-controllers">
					<?php /*Pagination*/ if ($links_pagination): ?>
						<div class="tzcontentslider-links-pagination"></div>
					<?php endif; ?>
					<?php /*Previous*/ if ($links_arrows): ?>
					<div class="tzcontentslider-links-prev">&laquo;</div>
					<?php endif; ?>
					<?php /*Play & Pause*/ if ($links_play_button): ?>
						<div class="tzcontentslider-links-play">&rsaquo;</div>
						<div class="tzcontentslider-links-pause">||</div>
					<?php endif; ?>	
					<?php /*Next*/ if ($links_arrows): ?>					
						<div class="tzcontentslider-links-next">&raquo;</div>
					<?php endif; ?>
					<div style="clear:both"></div>
				</div>
			<?php endif; ?>
			<div style="clear:both"></div>	
		</div>
		<?php endif; ?>
		<!--End Links Layout-->
		<div style="clear:both"></div>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
<?php if ($c_article_count > 0 && $article_animation!="disabled"): ?>
window.addEvent('load', function() {
	new tzslider({
		container: document.getElement('#tzcontentslider-<?php echo $modId; ?> .tzcontentslider-art-pages'),
		interval: <?php echo $article_animation_interval ?>,
		activator: "<?php echo $article_activator ?>",
		transition: "<?php echo $article_animation ?>",	
		fxOptions: {
			duration:  <?php echo $article_animation_speed ?>, 
			transition: Fx.Transitions.<?php echo $article_animation_transition ?>
		},
		buttons: {
			<?php if ($article_arrows): ?>
			previous: document.getElement('#tzcontentslider-<?php echo $modId; ?> .tzcontentslider-art-prev')
			,next: document.getElement('#tzcontentslider-<?php echo $modId; ?> .tzcontentslider-art-next')
			<?php endif; ?>
			<?php if ($article_play_button): ?>
			,play: document.getElement('#tzcontentslider-<?php echo $modId; ?> .tzcontentslider-art-play')
			,stop: document.getElement('#tzcontentslider-<?php echo $modId; ?> .tzcontentslider-art-pause')
			<?php endif; ?>
		}
		<?php if ($article_pagination): ?>
		,pagination: document.getElement('#tzcontentslider-<?php echo $modId; ?> .tzcontentslider-art-pagination')
		<?php endif; ?>
		,autoPlay: <?php echo $article_autoplay ?>
	});
});
<?php endif; ?>

<?php if ($links_block && $c_links_count!=0 && $links_animation!="disabled"): ?>
window.addEvent('load', function() {
	new tzslider({
		container: document.getElement('#tzcontentslider-<?php echo $modId; ?> .tzcontentslider-links-pages'),
		interval: <?php echo $links_animation_interval ?>,
		activator: "<?php echo $links_activator ?>",
		transition: "<?php echo $links_animation ?>",		
		fxOptions: {
			duration:  <?php echo $links_animation_speed ?>, 
			transition: Fx.Transitions.<?php echo $links_animation_transition ?>
		},
		buttons: {
			<?php if ($links_arrows): ?>
			previous: document.getElement('#tzcontentslider-<?php echo $modId; ?> .tzcontentslider-links-prev')
			,next: document.getElement('#tzcontentslider-<?php echo $modId; ?> .tzcontentslider-links-next')
			<?php endif; ?>
			<?php if ($links_play_button): ?>
			,play: document.getElement('#tzcontentslider-<?php echo $modId; ?> .tzcontentslider-links-play')
			,stop: document.getElement('#tzcontentslider-<?php echo $modId; ?> .tzcontentslider-links-pause')
			<?php endif; ?>
		}
		<?php if ($links_pagination): ?>
		,pagination: document.getElement('#tzcontentslider-<?php echo $modId; ?> .tzcontentslider-links-pagination')
		<?php endif; ?>
		,autoPlay: <?php echo $links_autoplay ?>
	});
});
<?php endif; ?>
//]]>
</script>