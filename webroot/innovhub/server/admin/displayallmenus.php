<?php
   $menus = $menu->getAllMenus(); 
?>

<table cellpadding="0" cellspacing="0"><tr><td>
<table cellpadding="3" cellspacing="1" bgcolor="#AAAAAA">
<tr bgcolor="#EEEEEE">
   <td> </td>
   <td>Menu Name</td>
   <td>Font size</td>
   <td>Font color / hover</td>
   <td>Background color / hover</td>
   <td>Background / left / right images</td>
   <td>Menu Short description</td>
   <td></td>
</tr>

<?php
   for ($i=0; $i<count($menus); $i++) {
      $line = $menus[$i];
?>
<tr bgcolor="#EEEEEE">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
   <input type="hidden" name="action" value="editmenu">
   <input type="hidden" name="menuid" value="<?php echo $line['menuid']; ?>">
   <td><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=displaysitemenu&adminmid=menus&menuid=<?php echo $line['menuid']; ?>">View</a></td>
   <td><input type="text" size="20" name="menutitle" value="<?php echo $line['menutitle']; ?>"></td>
   <td><input type="text" size="3" name="fs" value="<?php echo $line['fs']; ?>"></td>
   <td><input type="text" size="8" name="fc" value="<?php echo $line['fc']; ?>"><br><input type="text" size="8" name="fch" value="<?php echo $line['fch']; ?>"></td>
   <td><input type="text" size="8" name="bgc" value="<?php echo $line['bgc']; ?>"><br><input type="text" size="8" name="bgch" value="<?php echo $line['bgch']; ?>"></td>
   <td><input type="text" size="20" name="leftimg" value="<?php echo $line['leftimg']; ?>"><br><input type="text" size="20" name="rightimg" value="<?php echo $line['rightimg']; ?>"><br><input type="text" size="20" name="menubg" value="<?php echo $line['menubg']; ?>"></td>
   <td><textarea name="descr" rows="3" cols="25"><?php echo $line['descr']; ?></textarea></td>
   <td><input type="submit" name="subaction" value="Update"><br><input type="submit" name="subaction" value="Delete"></td>
   </form>
</tr>

<?php   } ?>

<tr bgcolor="#EEEEEE">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
   <input type="hidden" name="action" value="addmenu">
   <td></td>
   <td><input type="text" size="20" name="menutitle" value=""></td>
   <td><input type="text" size="3" name="fs" value=""></td>
   <td><input type="text" size="8" name="fc" value=""><br><input type="text" size="8" name="fch" value=""></td>
   <td><input type="text" size="8" name="bgc" value=""><br><input type="text" size="8" name="bgch" value=""></td>
   <td><input type="text" size="20" name="leftimg" value=""><br><input type="text" size="20" name="rightimg" value=""><br><input type="text" size="20" name="menubg" value=""></td>
   <td><textarea name="descr" rows="3" cols="25"></textarea></td>
   <td><input type="submit" name="subaction" value="Add"></td>
   </form>
</tr>
</table>
</td></tr></table>
