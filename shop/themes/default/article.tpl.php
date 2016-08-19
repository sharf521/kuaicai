<?php require 'header.php';?>
<? if($this->control=='article'){?>
    <div class="content-box">
        <?=$content?>
    </div>
<? }elseif($this->control=='promise'){?>
    <div class="content-box">
        <?=$content?>
    </div>
<? }?>
<?php require 'footer.php';?>
