<?php
                  if ($surveyOBJ->sectionsAnswered($survey_id, $vars['srvy_person_id'], "North America")) {
?>
                     <form name="pbrdirectory" id="pbrdirectory" action="http://www.plasticbagrecycling.org/04.0/s04.1.php" method="POST">
                     <input type="hidden" name="company_name" value="<?= $sci['company'] ?>">
                     <input type="hidden" name="contact_name" value="<?= $sci['contact_name'] ?>">
                     <input type="hidden" name="contact_email" value="<?= $sci['contact_email'] ?>">
                     <input type="hidden" name="address1" value="<?= $sci['address1'] ?>">
                     <input type="hidden" name="address2" value="<?= $sci['address2'] ?>">
                     <input type="hidden" name="city" value="<?= $sci['city'] ?>">
                     <input type="hidden" name="state_code" value="<?= $sci['state_code'] ?>">
                     <input type="hidden" name="zipcode" value="<?= $sci['zipcode'] ?>">
                     <input type="hidden" name="website" value="<?= $sci['website'] ?>">
                     <input type="hidden" name="tel" value="<?= $sci['tel'] ?>">
                     </form>
                     <a href="#" onclick="document.getElementById('pbrdirectory').submit();">Add your organization to plasticbagrecycling.org online directory.</a><br>

                     <form name="plasticsmarkets" id="plasticsmarkets" action="http://www.plasticsmarkets.org/jsfcode/controller.php" method="POST">
                     <input type="hidden" name="action" value="createCompany">
                     <input type="hidden" name="name" value="<?= $sci['company'] ?>">
                     <input type="hidden" name="contact_name" value="<?= $sci['contact_name'] ?>">
                     <input type="hidden" name="contact_title" value="<?= $sci['contact_title'] ?>">
                     <input type="hidden" name="email" value="<?= $sci['contact_email'] ?>">
                     <input type="hidden" name="address1" value="<?= $sci['address1'] ?>">
                     <input type="hidden" name="address2" value="<?= $sci['address2'] ?>">
                     <input type="hidden" name="city" value="<?= $sci['city'] ?>">
                     <input type="hidden" name="state" value="<?= $sci['state_code'] ?>">
                     <input type="hidden" name="zip" value="<?= $sci['zipcode'] ?>">
                     <input type="hidden" name="url" value="<?= $sci['website'] ?>">
                     <input type="hidden" name="phone" value="<?= $sci['tel'] ?>">
                     <input type="hidden" name="fax" value="<?= $sci['fax'] ?>">
                     </form>
                     <a href="#" onclick="document.getElementById('plasticsmarkets').submit();">Add your organization to plasticsmarkets.org online directory.</a>
<?php
                  }
?>
