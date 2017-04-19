<?php 
    // no direct access 
    defined('_JEXEC') or die('Restricted access'); 

    // Make sure jQuery is loaded!
    JHtml::_('jquery.framework');
    JHtml::stylesheet( $sfl_basepath."tablesorter/themes/red/style.css" );
    JHtml::stylesheet( $sfl_basepath."mod_simplefilelister.css" );
    JHtml::script( $sfl_basepath."mod_simplefilelister.js" );
    JHtml::script( $sfl_basepath."tablesorter/jquery.tablesorter.min.js" );

    $open_div = $close_div = '';

    // We're gonna have a fixed height DIV
    if ($sfl_maxheight > 0) {
        $open_div = '<div id="div_sflwrapper" style="position: relative; height: '.$sfl_maxheight.'px; overflow: auto; background:'.$sfl_bgcolor.'">';
        $close_div = '</div>';
    }
?>

<?php echo $open_div ?>

    <div id="div_sflcontent" class="sfl_content" style="background: <?php echo $sfl_bgcolor ?>; left: <?php echo $sfl_boxleft ?>px;">
        <span style="display: none"><a id="sfl_ARefresh" class="sfl_ARefresh" href="javascript:void(0);">Refresh</a></span>
        <?php echo $results; ?>
    </div>

<?php echo $close_div ?>
