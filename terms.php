<style type="text/css">
.tbrightcontent {
display: block;
position: fixed;
top: 15%;
left: 25%;
width: 50%;
height: 60%;
padding: 16px;
background-color: white;
z-index: 9999999;
overflow: auto;
text-align: left;
font-size: 15px;
}

.tdarkoverlay {
display: block;
position: fixed;
top: 0%;
left: 0%;
width: 100%;
height: 100%;
background-color: black;
z-index: 9999998;
-moz-opacity: 0.8;
opacity: .80;
filter: alpha(opacity=80);
}

h3.termstitle {
background: #C81F2C;
color: #fff;
text-align: center;
padding: 1%;
margin: -16px -16px 30px -16px;
}
</style>

<div id="tfade" class="tdarkoverlay"></div>

<div id="tlight" class="tbrightcontent">

		<div class="termspopupcontainer">
		
		<?php
		
		//Just a reminder. Values already set before.
		//$currentpostid = get_the_ID();
		//$termspageid = get_post_meta( $currentpostid, 'terms_selectedterms', true );

		if($termspageid) { 
			$termscontent=get_post($termspageid);?>
			<h3 class="termstitle"><?php echo $termscontent -> post_title?></h3>
			<?php echo apply_filters('the_content', $termscontent->post_content);
		}

		if( (get_post_meta( $termspageid, 'terms_agreetxt', true )) != '' ) {
			$tagree = get_post_meta( $termspageid, 'terms_agreetxt', true );
		}
		elseif( get_option('termsopt_agreetxt') != '' ) {
			$tagree = get_option('termsopt_agreetxt');
		}
		else {
			$tagree = 'I Agree';
		}
		
		if( (get_post_meta( $termspageid, 'terms_disagreetxt', true )) != '' ) {
			$tdisagree = get_post_meta( $termspageid, 'terms_disagreetxt', true );
		}
		elseif( get_option('termsopt_disagreetxt') != '' ) {
			$tdisagree = get_option('termsopt_disagreetxt');
		}
		else {
			$tdisagree = 'I Do Not Agree';
		}		
			?>
		
			<form method=post>
				<div class="tthebutton">
					<input class="termsagree" name="SubmitAgree" type="submit" value="<?php echo $tagree; ?>" />
					<input class="termsdecline" name="SubmitDecline" type="submit" value="<?php echo $tdisagree; ?>" />
				</div>
			</form>

		</div>
		
</div>