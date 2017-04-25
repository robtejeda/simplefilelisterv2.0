<?php 
    // no direct access 
    defined('_JEXEC') or die('Restricted access'); 

    // Make sure jQuery is loaded!
    JHtml::_('jquery.framework');
    JHtml::stylesheet( $sfl_basepath."mod_simplefilelister.css" );
    JHtml::stylesheet( $sfl_basepath."tablesorter/themes/red/style.css" );
    JHtml::stylesheet( $sfl_basepath."simplePagination/simplePagination.css" );
    JHtml::script( $sfl_basepath."mod_simplefilelister.js" );
    JHtml::script( $sfl_basepath."tablesorter/jquery.tablesorter.min.js" );
    JHtml::script( $sfl_basepath."simplePagination/jquery.simplePagination.js" );
    
    $rand = rand();
    $open_div = $close_div = '';

    // We're gonna have a fixed height DIV
    if ($sfl_maxheight > 0) {
        $open_div = '<div id="div_sflwrapper" style="position: relative; height: '.$sfl_maxheight.'px; overflow: auto; background:'.$sfl_bgcolor.'">';
        $close_div = '</div>';
    }
?>

<?php echo $_SERVER['']?>

    <div id="div_sflcontent" class="sfl_content" style="background: <?php echo $sfl_bgcolor ?>; left: <?php echo $sfl_boxleft ?>px;">
        
        <table width="100%" class="folder-lister-table tablesorter" style="text-align: left" id="documents-list-<?php echo $rand?>">
            <thead>
                <tr>
                    <th width="50%" class="header">Filename</th>
                    <th class="header">Date</th>
                    <th class="header">File Size (MB)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php

                    foreach ($results as $row) {

                        echo
                            '<tr class="paginate">
                                <td class="sfl_item">
                                    <nobr><a href="'.$row['href'].'">'.$row['filename'].'</a></nobr>
                                </td>
                                <td class="sfl_item text-right"><nobr>'.$row['date'].'</nobr></td>
                                <td class="sfl_item text-right"><nobr>'.$row['size'].'</nobr></td>
                                <td class="action-btn">
                                    <nobr>
                                        <a class="sfl_btnDelete" rel="'.$row['href'].'" href="javascript:void(0)")>
                                            <img class="sfldel" src="/modules/mod_simplefilelisterv1.0/images/delete.png">
                                        </a>
                                    </nobr>
                                </td>
                            </tr>';
                    }
                ?>

            </tbody>
        </table>

        <div id="pagination-<?php echo $rand?>"></div>
        <h3 id="message-<?php echo $rand?>">No files found</h3>

        <script type="text/javascript">

            jQuery(document).ready(function(){

                if(<?php echo count($results); ?> == 0) {
                    jQuery("#pagination-<?php echo $rand?>").hide();
                    jQuery("#documents-list-<?php echo $rand?>").hide();
                    jQuery("#message-<?php echo $rand?>").show();
                } else {
                    
                    jQuery("#message-<?php echo $rand?>").hide();
                }

                if(typeof jQuery("#documents-list-<?php echo $rand?>") !== "undefined") {
                    //Remove empty row
                    jQuery("#documents-list-<?php echo $rand?> tbody tr:first-child").remove();
                    //Instantiate table sorter on table
                    jQuery("#documents-list-<?php echo $rand?>")
                        .tablesorter({
                            headers: {
                                3: {
                                    sorter: false
                                }
                            }
                        });

                    var pageParts = jQuery("#documents-list-<?php echo $rand?> .paginate");

                    jQuery("#pagination-<?php echo $rand?>")
                        .pagination({
                            items: <?php echo count($results); ?>,
                            itemsOnPage: <?php echo $sfl_maxfiles; ?>,
                            cssStyle: 'light-theme',
                            onPageClick: function(pageNum) {

                                showItems(pageNum);
                            },
                            onInit: function() {

                                showItems(1);
                            }
                        });

                    function showItems(pageNum) {
                        // Which page parts do we show?
                        var start = <?php echo $sfl_maxfiles; ?> * (pageNum - 1);
                        var end = start + <?php echo $sfl_maxfiles; ?>;

                        // First hide all page parts
                        // Then show those just for our page
                        pageParts.hide()
                                 .slice(start, end).show();
                    }
                }
            });
        </script>          
    </div>

<?php echo $close_div ?>
