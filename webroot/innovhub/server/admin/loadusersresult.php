<?php
   $results = $vars['results'];
   $newusers = $results['new'];
   $existingusers = $results['existing'];
   $overrideusers = $results['override'];
   $deleteusers = $results['delete'];
   $emptyusers = $results['empty'];
?>
<h2>User Load Results</h2>
Successful users added: <?php echo count($newusers); ?><br>
Users that already existed: <?php echo count($existingusers); ?><br>
Rows not loaded because there was no email address: <?php echo count($emptyusers); ?><br>

<br>
<b>New Users:</b><br>
<?php for ($i=0; $i<count($newusers); $i++) print $newusers[$i]['email']." (".$newusers[$i]['fname']." ".$newusers[$i]['lname'].") <br>"; ?>

<br>
<b>Users Already in the system:</b><br>
<?php for ($i=0; $i<count($existingusers); $i++) print $existingusers[$i]['email']." (".$existingusers[$i]['fname']." ".$existingusers[$i]['lname'].") <br>"; ?>

<br>
<b>Users Already in the system, but overridden:</b><br>
<?php for ($i=0; $i<count($overrideusers); $i++) print $overrideusers[$i]['email']." (".$overrideusers[$i]['fname']." ".$overrideusers[$i]['lname'].") <br>"; ?>

<br>
<b>Users Already in the system and delted:</b><br>
<?php for ($i=0; $i<count($deleteusers); $i++) print $deleteusers[$i]['email']." (".$deleteusers[$i]['fname']." ".$deleteusers[$i]['lname'].") <br>"; ?>
