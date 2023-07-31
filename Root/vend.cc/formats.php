<?php
require("./header.php");
if ($checkLogin) {
?>
<style type="text/css">
<!--
.style2 {font-weight: bold}
-->
</style>

<div class="style2">
    <p>
    </p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>Our Formats Of Cards | </p>
    <p>&nbsp; </p>
    <p>      <br/>
      For Cards Fulls | </p>
    <p>[+] Australia Fulls </p>
    <p>[-] CCNum | CCMonth | CCYear | CCCVV | FirstName  LastName | Address | City | State | PostCode | Country | Phone | EMAIL | DOB | MMN | </p>
    <p>&nbsp;</p>
    <p>[+] United Stats Fulls</p>
    <p>[-] CCNum | CCMonth | CCYear | CCCVV | FirstName LastName | Address | City | State | PostCode | Country | Phone | EMAIL |DOB | MMN | SSN | VBV PASSWORD | </p>
    <p>&nbsp;</p>
    <p>[+] Canada Fulls  | </p>
    <p>[-] CCNum | CCMonth | CCYear | CCCVV | FirstName LastName | Address | City | State | PostCode | Country | Phone | EMAIL | DOB | MMN | PIN | SIN |</p>
    <p>&nbsp;</p>
    <p>[+] United Kingdom Fulls  | </p>
    <p>[-] CCNum | CCMonth | CCYear | CCCVV | FirstName LastName | Address | City | State | PostCode | Country | Phone | EMAIL | DOB | MMN | ACCOUNT | SORTCODE | PIN | BANK NAME |</p>
    <p>&nbsp;</p>
    <p>[+] For Cards Cvv Normal Info</p>
    <p>[-] USA,UK, EU</p>
    <p>CCNum | CCMonth | CCYear | CCCVV | FirstName LastName | Address | City | State | PostCode | Country | Phone |</p>
    <p>&nbsp;</p>
    <p>[+] For Cards With Dob</p>
    <p>[-] CCNum | CCMonth | CCYear | CCCVV | FirstName LastName | Address | City | State | PostCode | Country | Phone | DOB</p>
    <p>&nbsp;</p>
    <p>[+] For Dead Fulls and Profiles </p>
    <p>[-] Fullz Info USA Fresh Database - FirstName | LastName | MiddleName | Email | Password | Address | Phone | DOB | SSN | Driver License |</p> Bank Name | Bank Account Number | Bank Routing Number | Company Name | Current Years of Job | MMN |
    <p>
    </p>
</div>
<strong></br>
</br>

<?php
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>
</strong>