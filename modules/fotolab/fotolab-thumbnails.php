<?php html_rtl($lang); ?>
<!-- Generated by AceHTML Freeware http://freeware.acehtml.com -->
<!-- Creation date: 02.12.2001 -->
<head>
    <?php echo setCharSet(); ?>
    <title></title>
    <meta name="Description" content="">
    <meta name="Keywords" content="">
    <meta name="Author" content="Lorilla Bong">
    <meta name="Generator" content="AceHTML 4 Freeware">
    <script language="">
        <!-- Script Begin
    function previewpic(fn) {
            parent.PREVIEWFRAME.document.previewpic.src = fn;

        }
        //  Script End -->
    </script>
</head>
<body topmargin=3 marginheight=3>
    <?php
    if ($mode == "browse") {
        echo "<font face=verdana size=2>Pfad:";
        $basedir = dirname(stripcslashes($directory));
        echo $basedir . "<br>";
//chdir($basedir."\\");
        $handle = opendir($basedir . '.');
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {

                $fileext = strtolower(substr($file, strrpos($file, ".") + 1));
                if (($fileext == "gif") || ($fileext == "jpg") || ($fileext == "png")) {
                    echo '
			<a href="javascript:previewpic(\'' . str_replace("\\", "\\\\", $basedir) . '\\' . '\\' . $file . '\')" title="Anklicken zum Vorschau"><img src="' . $basedir . '\\' . $file . '" border=0 ';
                    $picheight = getimagesize($basedir . '/' . $file);
                    if ($picheight[1] > 75)
                        echo 'height="75"';
                    echo ' ></a>';
                }
            }
        }
        closedir($handle);
    }
    ?><br>
</body>
</html>
