<?php
/**
 * Шаблон формы поиска (searchform.php)
 * @package WordPress
 * @subpackage your-clean-template-3
 */
?>
<form role="search" method="get" class="search-form form-inline" action="<?php echo home_url( '/' ); ?>">
	<div class="form-group">
		<label class="sr-only" for="search-field">Поиск</label>
		<input type="search" class="form-control input-sm" id="search-field" placeholder="Строка для поиска" value="<?php echo get_search_query() ?>" name="s">
	</div>
	<button type="submit" class="btn btn-default btn-sm">Искать</button>
</form>

<!--<form role="search" method="get" class="search-form form-inline" action="<?php /*echo home_url( '/' ); */?>">-->
    <!--<input class="form-control" type="text" value="" placeholder="поиск">
    <i class="fa fa-search" aria-hidden="true"></i>-->

    <!--<div class="input-group">
        <input type="text" class="form-control" id="" placeholder="поиск">
        <div class="input-group-addon"><button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button></div>
    </div>

</form>-->