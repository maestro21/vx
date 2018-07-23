<?php $selectlist = (bool)(@$post['mode'] == 'selectFile');
$gid = $id;
?>
<?php if (!$selectlist) { ?>
<form name="addImgGal" enctype='multipart/form-data' action="<?php echo BASE_URL . $class;?>/save?ajax=1">
<h1><a href="<?php echo BASE_URL.$class;?>"><?php echo T('Galleries');?></a> &rarr; <?php echo T($data['name']);?>
    <a href="<?php echo BASE_URL.$class;?>/slider/<?=$id;?>" class="fa fa-picture-o icon file" target="_blank" title="<?=T('slider');?>"></a>
    <label for="galnewfile"><i class="fa fa-plus icon file"></i></label>
    <input type="file" name="galnewfile" id="galnewfile" />
    <input type="hidden" name="id" value="<?php echo $id;?>">
    <input type="hidden" name="slug" value="<?php echo $data['slug'];?>">
</h1>
</form>
<?php } ?>
<div>
    <?php if($data['img']) {
        foreach ($data['img'] as $item) {
            $id = $item['id'];
            ?>
            <div data-id="<?php echo $id; ?>" class="photothumb" <?php echo getThumb($item['fname'], $data['slug']);?>
                 data-img="<?php echo getImgById($item['id']);//fname'], $data['slug']);?>"
                 data-src="<?php echo getThumb($item['fname'], $data['slug'], false); ?>">
                <?php if (!$selectlist) { ?>
                    <a href="javascript:void(0)"
                       onclick="conf('<?php echo BASE_URL . $class; ?>/delimg/<?php echo $id; ?>', '<?php echo T('del conf'); ?>')"
                       class="fa-trash-o fa icon icon_sml tlcorner"></a>
                    <a href="javascript:void(0)"
                       onclick="makeMainPicture('<?php echo $id;?>')"
                       class="fa-picture-o fa icon icon_sml tlcorner"></a>
                <?php } ?>

                <!--<a href="<?php echo BASE_URL . $class; ?>/edit/<?php echo $id; ?>" target="_blank" class="fa-pencil fa icon icon_sml"></a>-->
                <input name="name-<?php echo $id; ?>" class="galedit" type="text" value="<?php echo $item['name']; ?>">
            </div>
        <?php }
    } else { echo T('empty gallery'); } ?>

</div>
<script>
    Shadowbox.init({
        skipSetup: true
    });

    $('.photothumb').each(function(index) {
        $(this).click(function() {
            <?php if ($selectlist) { ?>
                $('#<?php echo $post['uid'];?>').attr('value',$(this).data("id"));
                $('#<?php echo $post['uid'];?>-thumb').attr('src',$(this).data("src"));
                $('#<?php echo $post['uid'];?>-thumb').show();
            <?php } else {?>
            Shadowbox.open({
                content: $(this).data("img"),
                player: 'img'
            });

            <?php } ?>
          //  window.location = '<?php echo BASE_URL.$class;?>/' + $(this).data("id");
        });
    });

    $('#galnewfile').on('change', function() {
        $.ajax({
            url: '<?php echo BASE_URL . $class;?>/upimg/<?php echo $id;?>?ajax=1',
            data: new FormData($('form')[0]),
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success: function(data){
                processResponse(data);
            }
        });
    });

    function makeMainPicture(id) {
        $.ajax({
            url: '<?php echo BASE_URL . $class;?>/setmainpicture/<?php echo $gid . '/';?>' + id,
        });
    }
</script>
