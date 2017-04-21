<?php 
    // no direct access 
    defined('_JEXEC') or die('Restricted access'); 

    // Make sure jQuery is loaded!
    JHtml::_('jquery.framework');
    JHtml::stylesheet( $sfl_basepath."tablesorter/themes/red/style.css" );
    JHtml::stylesheet( $sfl_basepath."mod_simplefilelister.css" );
    JHtml::script( $sfl_basepath."mod_simplefilelister.js" );
    JHtml::script( $sfl_basepath."tablesorter/jquery.tablesorter.min.js" );
    
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

                <?php echo $results;

                    foreach ($results as $row) {

                    echo
                        '<tr>
                                <td class="sfl_item">
                                    <nobr><a href="'.$row['href'].'">'.$row['name'].'</a></nobr>
                                </td>
                                <td class="sfl_item text-right"><nobr>'.$row['date'].'</nobr></td>
                                <td class="sfl_item text-right"><nobr>'.$row['size'].'</nobr></td>
                                <td class="action-btn">
                                    <nobr>
                                        <a class="sfl_btnDelete" href="#" onclick=deleteFile("'.$row['name'].'")>
                                            <img class="sfldel" src="/modules/mod_simplefilelisterv1.0/images/delete.png">
                                        </a>
                                    </nobr>
                                </td>
                        </tr>';
                    }
                ?>

            </tbody>
        </table>

        <script type="text/javascript">
            jQuery(document).ready(function(){
                if(typeof jQuery("#documents-list-<?php echo $rand?>") !== "undefined") {
                    jQuery("#documents-list-<?php echo $rand?> tbody tr:first-child").remove();
                    jQuery("#documents-list-<?php echo $rand?>").tablesorter({ 
                        headers: { 
                            3: { 
                                sorter: false 
                            }
                        } 
                    });
                }
            });
        </script>          
    </div>

<?php echo $close_div ?>
