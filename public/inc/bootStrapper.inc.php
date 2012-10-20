<?php
/* 
 * Bootstrapper is a class to easily create bootstrap components.
*/
class BootStrapper {
	
	public function heroUnit($title,$content) {
		return '<div class="hero-unit">
        <h1>'.$title.'</h1>
        <p>'.$content.'</p>
      </div>';
	}
	public function row($content) {
		return '<div class="row">'.$content.'</div>';
	}
	
	public function errormessage($content) {
		return '<div class="alert alert-error">'.$content.'</div>';
	}
	
	public function successmessage($content) {
		return '<div class="alert alert-success">'.$content.'</div>';
	}
	
	public function block($size,$content) {
		return '<div class="span'.$size.'">'.$content.'</div>';
	}
	
	public function loginForm ($usernametext,$passwordtext,$signintext,$action) {
		return '<form class="form-horizontal" method="post" action="'.$action.'">
				  <div class="control-group">
					<label class="control-label" for="inputUsername">'.$usernametext.'</label>
					<div class="controls">
					  <input name="username" type="text" id="inputUsername" placeholder="'.$usernametext.'">
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="inputPassword">'.$passwordtext.'</label>
					<div class="controls">
					  <input name="password" type="password" id="inputPassword" placeholder="'.$passwordtext.'">
					</div>
				  </div>
				  <div class="control-group">
					<div class="controls">
					  <!--<label class="checkbox">
						<input type="checkbox"> Remember me
					  </label>-->
					  <button type="submit" class="btn">'.$signintext.'</button>
					</div>
				  </div>
				</form>';
	}
	
}
?>