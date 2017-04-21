<?php

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class ModSimpleFileListerHelperv10 {

    function getFileList(
        $params,
        $sfl_dirlocation,
        $sfl_basepath,
        $sfl_maxfiles,
        $sfl_userlocation) {

        $results = "";
        $session =& JFactory::getSession();

        $session->set( 'sfl_startdir', '');
        $session->set( 'sfl_userdir', '');

        $results = "";

        if (strlen($sfl_dirlocation) == 0 && strlen($sfl_userlocation) == 0) {

            $results .= JText::_('NO_DIR_GIVEN');
        } else {

            echo "<script>window.sfl_dirlocation= '".$sfl_dirlocation."';</script>";
            $results .= ModSimpleFileListerHelperv10::getDirContents($params, $sfl_dirlocation, $sfl_basepath, $sfl_maxfiles, $sfl_userlocation);
        }

        return $results;
    }
    
    function getFileSizePP($filesize) {

        if(is_numeric($filesize)) {

            $decr = 1024; $step = 0;
            $prefix = array('Bytes','KB','MB','GB','TB','PB');

            while(($filesize / $decr) > 0.9){

                $filesize = $filesize / $decr;
                $step++;
            }

                return round($filesize,2).' '.$prefix[$step];

        } else {

            return 'NaN';
        }
    }

    function getDirContents($params, $sfl_dirlocation, $sfl_basepath, $sfl_maxfiles, $sfl_userlocation) {

        $session =& JFactory::getSession();
        $results = "";
        $sfl_goupdir = "";
        $sfl_currentdir = "";
        $browsedir = "";
        $filelist = "";
        $sfl_dirlocationdefault = $params->get( 'sfl_dirlocation', '.'.DIRECTORY_SEPARATOR.'images' );
        $sfl_next = $params->get( 'sfl_next', '0' );
        $sfl_showfilesize = $params->get( 'sfl_showfilesize', '0' );
        $sfl_onlyimg = $params->get( 'sfl_onlyimg', '0' );
        $sfl_imgthumbs = $params->get( 'sfl_imgthumbs', '0' );
        $sfl_thumbheight = $params->get( 'sfl_thumbheight', '30' );
        $sfl_thumbwidth = $params->get( 'sfl_thumbwidth', '30' );
        $sfl_thumbkeepaspect = $params->get( 'sfl_thumbkeepaspect', '0' );
        $sfl_listdir = $params->get( 'sfl_listdir', '0' );
        $sfl_browsedir = $params->get( 'sfl_browsedir', '0' );
        $sfl_showdir = $params->get( 'sfl_showdir', '1');
        $sfl_showicon = $params->get( 'sfl_showicon', '1');
        $sfl_showsort = $params->get( 'sfl_showsort', '0' );
        $sfl_setbasepath = $params->get( 'sfl_basepath', '');
        $sfl_basepathusr = $params->get( 'sfl_basepathusr', '');
        $sfl_listleft = $params->get( 'sfl_listleft', '-10' );
        $sfl_allowdelete = $params->get( 'sfl_allowdelete', '0' );
        $sfl_allowdeleteall = $params->get( 'sfl_allowdeleteall', '0' );
        $sfl_allowdeletereg = $params->get( 'sfl_allowdeletereg', '0' );
        $sfl_allowdeleteedt = $params->get( 'sfl_allowdeleteedt', '0' );
        $sfl_movedeleted = $params->get( 'sfl_movedeleted', '0' );
        $sfl_movedeletedpath = $params->get( 'sfl_movedeletedpath', '' );
        $sfl_disablegdthreshold = $params->get( 'sfl_disablegdthreshold', '0' );
        $sfl_allowupdir = $params->get( 'sfl_allowupdir', '0' );
        $subdirlocation = "";
        $tmpSort = $session->get( 'sfl_sort', '');
        $show_delete = "0";

        $tds = "";
        $rows = [];

        // Don't allow moving upwards in dirs through AJAX
        if ($sfl_allowupdir == 0 && strlen(strstr($sfl_dirlocation, "../")) > 0) $sfl_dirlocation = $sfl_dirlocationdefault;

        if (strlen(strstr($sfl_dirlocation, $sfl_dirlocationdefault)) <= 0) $sfl_dirlocation = $sfl_dirlocationdefault;

        $baseurl = ModSimpleFileListerHelperv10::getBaseURL($sfl_dirlocation, $sfl_setbasepath);

        // Remove final slash to get dir. 
        if ( substr( $sfl_dirlocation , strlen($sfl_dirlocation) - 1) === DIRECTORY_SEPARATOR )

            $sfl_dirlocation = substr( $sfl_dirlocation, 0, strlen($sfl_dirlocation) - 1);

        $startdir = $session->get( 'sfl_startdir', '');

        if ($startdir === '')

            $session->set( 'sfl_startdir', $sfl_dirlocation);


        // Open directory
        if($bib = @opendir($sfl_dirlocation)) {

            $idx = 0;
            $dir_list = null;
            $file_list = null;
            $idx_startat = $session->get( 'sfl_nextindex', 0);
            $idx_endat = $session->get( 'sfl_stopindex', $sfl_maxfiles);

            while (false !== ($lfile = readdir($bib))) {

                if (is_dir($sfl_dirlocation.DIRECTORY_SEPARATOR.$lfile)) {

                    // Safe it, dir or filenames can't contain a *
                    if ($lfile !== "." && $lfile !== ".." && $sfl_listdir == 1)
                        $dir_list[] =  array( "sort" => strtolower($lfile), "name" => "*dir*".$lfile );
                } else {
                    $file_list[] = array( "sort" => strtolower($lfile), "name" => $lfile );
                }
            }

            if ($sfl_listdir == 1 && is_array($dir_list) && is_array($file_list))
                $full_list = array_merge($dir_list, $file_list);

            elseif (is_array($file_list))
                $full_list = $file_list;

            elseif ($sfl_listdir == 1 && is_array($dir_list))
                $full_list = $dir_list;

            else
                $full_list = null;

            if (is_array($full_list)) {

                foreach ($full_list as $lfile) {

                    $fdir = (substr($lfile['name'], 0, 5) === "*dir*");

                    if($lfile['name'] != "." && $lfile['name'] != ".." && !preg_match("/^\..+/", $lfile['name']) && $lfile['name'] != "index.html") {

                        // Capture a list of files to be put in session var. This to protect delete
                        $filelist .= $lfile['name'].'*';

                        if ($idx >= $idx_endat) {

                            $session->set( 'sfl_nextindex', $idx);
                            $session->set( 'sfl_stopindex', $idx + $sfl_maxfiles);
                            break;
                        }

                        $idx += 1;

                        $row['href'] = $sfl_dirlocation.DIRECTORY_SEPARATOR.$lfile['name'].'">'.$lfile['name'];
                        $row['filename'] = $lfile['name'];
                        $row['date'] = date ("M j, Y g:i A", filemtime($sfl_dirlocation.DIRECTORY_SEPARATOR.$lfile['name']));
                        $row['size'] = ModSimpleFileListerHelperv10::getFileSizePP(filesize($sfl_dirlocation.DIRECTORY_SEPARATOR.$lfile['name']));
                        
                        $rows[] = $row;

                        $tds .=  
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
                    }
                }

                $session->set( 'sfl_filelist', $filelist);
            }

            closedir($bib);

        return json_encode($rows);
    }

    function getBaseURL($sfl_dirlocation, $sfl_basepath) {

        $baseurl = "";
        $serverurl = "";
        $protocol = "";
        $protocol = "http://";
        $dirlocation = $sfl_dirlocation;

        if (strlen($sfl_basepath) == 0) {

            $tmp_dirlocation = str_replace("\\", "/", $dirlocation);

            if (substr(JURI::base(), 0, 5) === "https") {
                $protocol = "https://";
            }

            $folder = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/"));

            if ($folder === "//") {
                $folder = "";
            }

            if (strpos($dirlocation, "../") >= 0) {

                $dirlocation = realpath($dirlocation);
                $server_root = $_SERVER["DOCUMENT_ROOT"];

                //SCRIPT_FILENAME
                $dirlocation = str_replace($server_root, "/", $dirlocation);

                // Get rid of http:// or https://
                $server_basedir = str_replace("http://", "https://", $_SERVER["HTTP_HOST"]);
                $server_basedir = explode(".", $server_basedir);

                if ($server_basedir[0] === substr($dirlocation, 2, strlen($server_basedir[0])))
                    $dirlocation = ".".substr($dirlocation, strlen($server_basedir[0])+2);

                if (substr($dirlocation, 0, 2) === "//")
                    $dirlocation = str_replace("//", "./", $dirlocation);
            }

            if (strpos($tmp_dirlocation, ":/") >= 0 || substr($tmp_dirlocation, 0, 1) === "/") {

                //We have a root path, check and see if it is under Server root, e.g. make http:// url
                $sfl_realdirlocation = realpath($dirlocation);
                $sfl_realdirlocation = str_replace("\\", "/", $sfl_realdirlocation);
                $server_root = str_replace("\\", "/", realpath($_SERVER["DOCUMENT_ROOT"]));
                $server_path = str_replace("\\", "/",str_replace($server_root, "", str_replace("index.php", "", realpath($_SERVER["SCRIPT_FILENAME"]))));

                if (strlen(str_replace($server_root, "", $sfl_realdirlocation)) < strlen($sfl_realdirlocation)) {

                    //Path is in server root
                    $dirlocation = str_replace($server_root, ".", $sfl_realdirlocation);

                    if (strpos($dirlocation, $server_path) >= 0) {
                        $dirlocation = str_replace($server_path, "/", $dirlocation);
                    }
                }
            }

            // Check if relative path
            if (substr($dirlocation, 0, 1) === ".") {

                // Don't replace all dots... Could be dots in directory name!!!
                $serverurl .= $protocol.$_SERVER["HTTP_HOST"].$folder . substr($dirlocation, 1);
                $baseurl .= str_replace("\\", "", $serverurl);

            } else {

                if ((substr($dirlocation, 1, 2) === ":\\") || (substr($dirlocation, 0, 1) === "/")) {

                    // Server root path
                    $baseurl = "file://".str_replace("\\", "/", $dirlocation);

                } else {

                    $serverurl = str_replace("\\", "/", $_SERVER["DOCUMENT_ROOT"]);
                    $baseurl = str_replace("\\", "/", $dirlocation);
                    $baseurl = str_replace($serverurl, "", $baseurl);
                    $baseurl = $protocol.$_SERVER["HTTP_HOST"].$folder."/".$baseurl;
                }
            }
        } else {

            $baseurl = $sfl_basepath;
        }

        //Replace space with %20 for URL
        $baseurl = str_replace(" ", "%20", $baseurl);

        // Make sure it ends with front slash
        if ( substr( $baseurl , strlen($baseurl) - 1) !== "/" ) {

            $baseurl .= "/";
        }

        return $baseurl;
    }
}

class SFLAjaxServlet {

    function getContent($action, $params, $sfl_dirlocation, $sfl_basepath, $sfl_maxfiles, $sfl_userlocation, $sfl_file) {

        $content = ModSimpleFileListerHelperv10::getDirContents($params, $sfl_dirlocation, $sfl_basepath, $sfl_maxfiles, $sfl_userlocation);
        $app = JFactory::getApplication();
        $app->close();

        return $content;
    }
}
