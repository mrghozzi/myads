<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){ ?>
<hr />
<link href="<?php echo $url_site;  ?>/templates/_panel/css/codemirror.css" rel='stylesheet' type='text/css' />
<form action="<?php echo $url_site;  ?>/requests/<?php if(isset($_GET['id'])){ echo "edit_widgets"; }else{ echo "add_widgets"; } ?>.php?name=<?php echo $bname; ?>" method="POST">
<div class="form-input small active">
<label for="profile-name">Name</label>
<input type="text" name="name" <?php if(isset($_GET['id'])){ echo " value=\"{$abwidgets['name']}\" "; } ?> >
</div>
<hr />
<div class="form-input">
<textarea id="code" name="txt"><?php if(isset($_GET['id'])){ echo $abwidgets['o_valuer']; } ?></textarea>
</div>
<?php if(!isset($_GET['id'])){  ?>
<hr />
<div class="form-select">
<label for="place">place</label>
<select id="place" name="plas">
<option value="1">portal_left</option>
<option value="2">portal_right</option>
<option value="3">forum_left</option>
<option value="4">forum_right</option>
<option value="5">directory_left</option>
<option value="6">directory_right</option>
<option value="7">profile_left</option>
<option value="8">profile_right</option>
</select>
<!-- FORM SELECT ICON -->
<svg class="form-select-icon icon-small-arrow">
<use xlink:href="#svg-small-arrow"></use>
</svg>
<!-- /FORM SELECT ICON -->
</div>
<?php }  ?>
<hr />
<div class="form-input small active">
<label for="profile-name">Order</label>
<input type="number" name="p_order" <?php if(isset($_GET['id'])){ echo " value=\"{$abwidgets['o_order']}\" "; }else{ echo " value=\"0\" "; } ?> required>
</div>
<hr />
<div class="form-row split">
<?php if(isset($_GET['id'])){  ?>
     <input type="hidden" name="id_w" value="<?php echo $abwidgets['id']; ?>" />
     <!-- FORM ITEM -->
     <div class="form-item">
      <button class="btn btn-info" type="submit" name="submit" ><?php echo $lang['edit'];  ?></button>
     </div>
     <!-- /FORM ITEM -->

     <!-- FORM ITEM -->
     <div class="form-item">
      <b class="btn btn-danger" id="delete" ><?php echo $lang['delete'];  ?></b>
     </div>
     <!-- /FORM ITEM -->
<?php }else{  ?>
     <!-- FORM ITEM -->
     <div class="form-item">
      <button class="btn btn-info" type="submit" name="submit" ><?php echo $lang['add'];  ?></button>
     </div>
     <!-- /FORM ITEM -->
<?php }  ?>
     <!-- FORM ITEM -->
     <div class="form-item">
      <button class="btn btn-secondary" id="close" ><?php echo $lang['close'];  ?></button>
     </div>
     <!-- /FORM ITEM -->
</div>
</form>
<script type="text/javascript" src="<?php echo $url_site;  ?>/templates/_panel/js/codemirror.js"></script>
<script>
var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
  lineNumbers: true,
  extraKeys: {"Ctrl-Space": "autocomplete"},
  mode: {name: "javascript", globalVars: true}
});

if (typeof Promise !== "undefined") {
  var comp = [
    ["here", "hither"],
    ["asynchronous", "nonsynchronous"],
    ["completion", "achievement", "conclusion", "culmination", "expirations"],
    ["hinting", "advise", "broach", "imply"],
    ["function","action"],
    ["provide", "add", "bring", "give"],
    ["synonyms", "equivalents"],
    ["words", "token"],
    ["each", "every"],
  ]

  function synonyms(cm, option) {
    return new Promise(function(accept) {
      setTimeout(function() {
        var cursor = cm.getCursor(), line = cm.getLine(cursor.line)
        var start = cursor.ch, end = cursor.ch
        while (start && /\w/.test(line.charAt(start - 1))) --start
        while (end < line.length && /\w/.test(line.charAt(end))) ++end
        var word = line.slice(start, end).toLowerCase()
        for (var i = 0; i < comp.length; i++) if (comp[i].indexOf(word) != -1)
          return accept({list: comp[i],
                         from: CodeMirror.Pos(cursor.line, start),
                         to: CodeMirror.Pos(cursor.line, end)})
        return accept(null)
      }, 100)
    })
  }

  var editor2 = CodeMirror.fromTextArea(document.getElementById("synonyms"), {
    extraKeys: {"Ctrl-Space": "autocomplete"},
    lineNumbers: true,
    lineWrapping: true,
    mode: "text/x-markdown",
    hintOptions: {hint: synonyms}
  })
}
</script>
<script>
    $(document).ready(function(){
        $('#close').click(function(e){
          var wname=$(this).val();
          $("#widget_block").html('');
        });
    });
</script>
<?php } ?>
