<?php
global $homey_local;
$booking_hide_fields = homey_option('booking_hide_fields');

if($booking_hide_fields['guests'] != 1) {
?>
<div class="search-guests single-guests-js">
	<input name="guests" readonly id="guests" type="text" class="form-control" autocomplete="off" placeholder="<?php echo esc_attr(homey_option('srh_guests_label')); ?>">
	<input type="hidden" name="adult_guest" id="adult_guest" value="0">
	<input type="hidden" name="child_guest" id="child_guest" value="0">
	<div class="search-guests-wrap single-form-guests-js clearfix">
	
		<div class="adults-calculator">
			<span class="quantity-calculator homey_adult">0</span>
			<span class="calculator-label"><?php echo esc_attr(homey_option('srh_adults_label')); ?></span>
			<button class="adult_plus btn btn-secondary-outlined" type="button"><i class="fa fa-plus" aria-hidden="true"></i></button>
			<button class="adult_minus btn btn-secondary-outlined" type="button"><i class="fa fa-minus" aria-hidden="true"></i></button>
		</div>

		<?php if($booking_hide_fields['children'] != 1) { ?>
		<div class="children-calculator">
			<span class="quantity-calculator homey_child">0</span>
			<span class="calculator-label"><?php echo esc_attr(homey_option('srh_child_label')); ?></span>
			<button class="child_plus btn btn-secondary-outlined" type="button"><i class="fa fa-plus" aria-hidden="true"></i></button>
			<button class="child_minus btn btn-secondary-outlined" type="button"><i class="fa fa-minus" aria-hidden="true"></i></button>
		</div>
		<?php } ?>
		<div class="guest-apply-btn">
			<button id="apply_guests" class="btn btn-primary" type="button"><?php echo esc_attr($homey_local['sr_apply_label']); ?></button>
		</div><!-- guest-apply-btn -->
	</div><!-- search-guests -->
</div>
<?php } ?>