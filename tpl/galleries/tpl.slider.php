
<?php if($data['img']) { ?>
	<div class="gal">
    <?php foreach ($data['img'] as $item) { ?>
       <div class="pic" style="background-image:url('<?php echo getImgById($item['id']);?>')"></div>
    <?php } ?>
    </div>
<?php } ?>


						