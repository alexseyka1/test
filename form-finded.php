<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&coordorder=longlat" type="text/javascript"></script>
<div class="inner" style="background: #e3f2fd;padding-bottom: 1rem;">
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
<div class="results">