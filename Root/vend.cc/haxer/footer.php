			</div>
			<div id="copyright">
				SH0PZ0NE.WS<br/>
<?php
	//Please do not delete this line
?>
				sh0pz0ne 1.0 - &copy; Online Shopping cart 2012/2013<br/>
				<?php
				$mtime = explode(' ', microtime());
				$totaltime = $mtime[0] + $mtime[1] - $starttime;
				printf('Page loaded in %.3f seconds.', $totaltime);
				?>
				<br/>
                Support Contacts: ICQ 648577071
			</div>
		</div>
	</body>
</html>
<?php
	$db->close();
?>