<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Set the title
$TPL->SetTitle('Terms of Use');
//CSS
$TPL->AddCSS('template/style/page-terms-of-use.css');
//Print the header
$TPL->LoadHeader();

?>
<div class="content_holder">

 <div class="sub-page-title">
  <div id="title"><h1>Register<p></p><span></span></h1></div>
 </div>
 
  	<div class="container_2" align="center">
    
    	<!-- TERMS OF USAGE -->
        	<div class="container_3 terms-of-usage" align="center">
            	<h1>Warcry WoW Terms</h1>
                
                <!-- SCROLL BAR Container -->
                
                <script type="text/javascript" src="template/js/jquery.tinyscrollbar.min.js"></script>
                <script type="text/javascript">
					$(document).ready(function()
					{
						$('#terms-container').tinyscrollbar();
						
						$('#i-agree').click(function(e)
						{
							//redirect to the registration page
                            $.get(
								'ajax.php?phase=11',
								function(data)
								{
									//redirect only if the domain is valid
									if (data.indexOf('<?php echo $config['BaseURL']; ?>') > -1)
									{
										window.location = data;
									}
								}
							);
                        });
					});
				</script>	
                
                <div id="terms-container">
                    <div class="scrollbar"><div class="track"><div class="thumb"></div></div></div>
                    <div class="terms-shadow"></div>
                    <div class="viewport">
			 			<div class="overview">
<h3>TERMS OF USE</h3>
<p>
By accessing or using www.warcry-wow.com (the "Site") and affiliated services (the "Services") that belongs to Warcry WoW (Warcry), you (the "User") agree to comply with the terms and conditions governing the User's use of any areas of the Site and affiliated services as set forth below.
</p>
<h3>USE OF SITE</h3>
<p>
This Site or any portion of the Site as well as the Services may not be reproduced, duplicated, copied, sold, resold, or otherwise exploited for any commercial purpose except as expressly permitted by Warcry. Warcry reserves the right to refuse service in its discretion, without limitation, if Warcry believes that User conduct violates applicable law or is harmful to the interests of Warcry, other users of the Site and the Services or its affiliates. 
</p>
<h3>SITE ACCOUNT</h3>
<p>
You may register a regular account and password for the service for free. You are responsible for all activity under your account, associated accounts, and passwords. The Site is NOT responsible for unauthorised access to your account, and any loss of virtual items associated with it. 
</p>
<h3>ACCESS TO THE SITE AND THE SERVICES</h3>
<p>
Warcry provides free and unlimited access to the Site and the Services. 
</p>
<h3>SUBMISSION</h3>
<p>
Warcry does not assume any obligation with respect to any Submission and no confidential or fiduciary understanding or relationship is established by the Site's receipt or acceptance of any submission. All submissions become the exclusive property of the Site and its affiliates. The Site and its affiliates may use any submission without restriction and the User shall not be entitled to any compensation. 
</p>
<h3>VERIFICATION</h3>
<p>
THE USER MAY BE REQUIRED TO UNDERGO A VERIFICATION PROCEDURE INCLUDING, AND NOT LIMITED TO, SUBMISSION OF NECESSARY INFORMATION AND/OR DOCUMENTS TO ENSURE LEGITIMACY OF ANY PAYMENTS OR DONATIONS SHOULD WE CONSIDER ANY PAYMENT OR DONATION SUSPICIOUS. ACCOUNTS UNDERGOING VERIFICATION PROCEDURE REMAIN DISABLED UNTIL VERIFICATION PROCEDURE IS COMPLETE. SUBMITTED INFORMATION MAY BE DISCLOSED TO OUR AFFILIATES IN OUR MUTUAL EFFORTS TO PREVENT UNAUTHORISED PAYMENTS/DONATIONS. REQUESTED INFORMATION IS TO BE SUBMITTED BY EMAIL/FAX/ONLINE FORM AND MAY INCLUDE VERIFICATION OF THE USER'S IDENTITY. 
</p>
<h3>Third-Party Content</h3>
<p>
Neither Warcry, nor its affiliates, nor any of their respective officers, directors, employees, or agents, nor any third party, including any Provider/Affiliate, or any other User of the Site and Services, guarantees the accuracy, completeness, or usefulness of any content, nor its merchantability or fitness for any particular purpose. In some instances, the content available through the Site may represent the opinions and judgments of Providers/Affiliates or Users. Warcry and its affiliates do not endorse and shall not be responsible for the accuracy or reliability of any opinion, advice, or statement made on the Site and the Services by anyone other than authorised Warcry employees. Under no circumstances shall Warcry, or its affiliates, or any of their respective officers, directors, employees, or agents be liable for any loss, damage or harm caused by a User's reliance on information obtained through the Site and the Services. It is the responsibility of the User to evaluate the information, opinion, advice, or other Content available through this Site. 
</p>
<h3>Disclaimers and Limitation of Liability</h3>
<p>
User agrees that use of the Site and the Services is at the User's sole risk. Neither Warcry, nor its affiliates, nor any of their respective officers, directors, employees, agents, third-party content providers, merchants, sponsors, licensors or the like (collectively, "Providers"), warrant that the Site and the Services will be uninterrupted or error-free; nor do they make any warranty as to the results that may be obtained from the use of the Site and the Services, or as to the accuracy, reliability, or currency of any information content, service, or merchandise provided through this Site. THE SITE AND THE SERVICES ARE PROVIDED BY Warcry ON AN "AS IS" AND "AS AVAILABLE" BASIS. THE SITE MAKES NO REPRESENTATIONS OR WARRANTIES OF ANY KIND, EXPRESS OR IMPLIED, AS TO THE OPERATION OF THE SITE, THE INFORMATION, CONTENT, MATERIALS OR PRODUCTS, INCLUDED ON THIS SITE. TO THE FULL EXTENT PERMISSIBLE BY APPLICABLE LAW, THE SITE DISCLAIMS ALL WARRANTIES, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO, IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. Warcry WILL NOT BE LIABLE FOR ANY DAMAGES OF ANY KIND ARISING FROM THE USE OF THE SITE AND THE SERVICES, INCLUDING BUT NOT LIMITED TO DIRECT, INDIRECT, INCIDENTAL, PUNITIVE AND CONSEQUENTIAL DAMAGES. Under no circumstances shall Warcry or any other party involved in creating, producing, or distributing the Site and the Services be liable for any direct, indirect, incidental, special, or consequential damages that result from the use of or inability to use the Site and the Services, including but not limited to reliance by the User on any information obtained from the Site or that result from mistakes, omissions, interruptions, deletion of files or email, errors, defects, viruses, delays in operation or transmission, or any failure of performance, whether or not resulting from acts of God, communications failure, theft, destruction, or unauthorised access to the Site's records, programs, or services. The user hereby acknowledges that these disclaimers and limitation on liability shall apply to all content, merchandise, and services available through the Site and the Services. In states that do not allow the exclusion of limitation or limitation of liability for consequential or incidental damages, the User agrees that liability in such states shall be limited to the fullest extent permitted by applicable law. 
</p>
<h3>TERMINATION OF SERVICE</h3>
<p>
Warcry reserves the right, in its sole discretion, to change, suspend, limit, or discontinue any aspect of the Service and the Services at any time. Warcry may suspend or terminate any User's access to all or part of the Site and the Services, without notice, for any conduct that Warcry, in its sole discretion, believes is in violation of these terms and conditions. 
</p>
<h3>Fees and Payments</h3>
<p>
Warcry reserves the right, in its sole discretion, at any time to charge fees for access to and use of the Site and the Services, or any portions of the Site and the Services. If Warcry elects to charge fees, it will post notice on the Site of all provisions pertaining to fees and payments. By accepting donations from the User Warcry reserves the right to provide premium Services to the User. Donations are made on volunteer basis to support the Site and the Services. Warcry does not assume any financial obligation with respect to donations. 
</p>
<h3>ACKNOWLEDGEMENT</h3>
<p>
By accessing or using the Site and the Services, THE USER AGREES TO BE BOUND BY THESE TERMS AND CONDITIONS, INCLUDING DISCLAIMERS. Warcry reserves the right to make changes to the Site and these terms and conditions, including disclaimers, at any time. IF YOU DO NOT AGREE TO THE PROVISIONS OF THIS AGREEMENT OR ARE NOT SATISFIED WITH THE SERVICE, YOUR SOLE AND EXCLUSIVE REMEDY IS TO DISCONTINUE YOUR USE OF THE SERVICE. 
</p>
<h3>PRIVACY STATEMENT</h3>
<p>
Certain user information collected through the use of this website is automatically stored for reference. We track such information to perform internal research on our users' demographic interests and behaviour and to better understand, protect and serve our community of users. Payment or any other financial information is NEVER submitted, disclosed or stored on the Site and is bound to Terms and Conditions and Privacy Policy of our respective partners and/or payment processors. Basic user information (such as IP address, logs for using website interface and account management) may be disclosed to our partners in mutual efforts to counter potential illegal activities. Warcry makes significant effort to protect..
</p>         
                    	</div>
                    </div>
                    <div class="clear"></div>
                </div>
                <!-- SCROLL BAR Container . End -->	
                	<div style="height:20px;"></div>
                <input type="submit" class="agree" id="i-agree" value="I Agree the Terms of Warcry" style="margin:10px 0 0 0;" />
                
                <a class="dissagree" href="<?php echo $config['BaseURL']; ?>/index.php?page=home">I DO NOT AGREE !</a>
                
            </div>
    	<!-- TERMS OF USAGE . End -->
        
    </div>
    
</div>

<?php
	$TPL->LoadFooter();
?>
