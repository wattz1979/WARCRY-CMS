<?PHP
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Listen for permission errors
if ($error = $ERRORS->DoPrint('permissions'))
{
	echo $error;
}
unset($error);

?>
<!-- Secondary navigation -->
<nav id="secondary">
	<ul>
		<li class="current"><a href="#maintab">Statistics</a></li>
		<li><a href="#secondtab">Second Tab</a></li>
		<li><a href="#thirdtab">Settings</a></li>
	</ul>
</nav>
          
<!-- The content -->
<section id="content">

              <div class="tab" id="maintab">
                <h2>Quick actions</h2>

<a href="#" class="button icon edit">
  Create a new blogpost
</a>

<a href="#" class="button icon settings">
  Update system
</a>

<a href="#" class="button icon user">
  Manage users
</a>

<a href="#" class="button icon rss">
  Notify some people
</a>

<div class="column left twothird">
  <h2>Recent visitors</h2>
  <table class="areachart">
    <thead>
      <tr>
        <td></td>
        <th scope="col">Jan</th>
        <th scope="col">Feb</th>
        <th scope="col">Mar</th>
        <th scope="col">Apr</th>
        <th scope="col">May</th>
        <th scope="col">Jun</th>
      </tr>
    </thead>

    <tbody>
      <tr>
        <th scope="row">2009</th>
        <td>24</td>
        <td>29</td>
        <td>47</td>
        <td>56</td>
        <td>23</td>
        <td>12</td>
      </tr>
      <tr>
        <th scope="row">2010</th>
        <td>12</td>
        <td>18</td>
        <td>23</td>
        <td>64</td>
        <td>43</td>
        <td>35</td>
      </tr>
      <tr>
        <th scope="row">2011</th>
        <td>8</td>
        <td>43</td>
        <td>48</td>
        <td>32</td>
        <td>12</td>
        <td>56</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="column right third">
  <h2></h2>
  
</div>

<div class="clear"></div>
              </div>
              
              <div class="tab" id="secondtab">
                <h2>Explanation tabs</h2>
                <p>Your secondary menu should look something like this:</p>
<pre>
&lt;nav id="secondary"&gt;
  &lt;ul&gt;
    &lt;li class="current"&gt;&lt;a href="#maintab"&gt;Main tab&lt;/a&gt;&lt;/li&gt;
    &lt;li&gt;&lt;a href="#secondtab"&gt;Explanation tabs&lt;/a&gt;&lt;/li&gt;
    &lt;li&gt;&lt;a href="#thirdtab"&gt;Optional third tab&lt;/a&gt;&lt;/li&gt;
  &lt;/ul&gt;
&lt;/nav&gt;</pre>
                <p>If you use class="current" on a &lt;li&gt;-tag, that tab will be the default.</p>
                <p>In your content area, you must use the following structure:</p>
<pre>
&lt;div class="tab" id="maintab"&gt;
&lt;/div&gt;

&lt;div class="tab" id="secondtab"&gt;
&lt;/div&gt;

&lt;div class="tab" id="thirdtab"&gt;
&lt;/div&gt;
</pre>
              </div>
              
              <div class="tab" id="thirdtab">
                <h2>Third tab</h2>
              </div>
