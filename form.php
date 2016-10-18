<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<div class="outer">
    <div class="inner">
        <form>
            <?php if(!empty($_GET['search'])): ?>
                <span>
                    <input class="search" type="text" name="search" value="<?php echo $_GET['search'];?>">
                </span>
            <?php else: ?>
                <span>
                    <input class="search" type="text" name="search" placeholder="Москва Трубецкая улица дом 48">
                </span>
            <?php endif; ?>
            <span>
                <input type="submit" value="Найти">
            </span>
        </form>
    </div>
</div>