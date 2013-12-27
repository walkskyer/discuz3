<?php exit;?>
<form enctype="multipart/form-data" method="post" action="home.php?mod=spacecp&ac=plugin&id=lucas_watermark:memcp&pluginop=update">
<input type="hidden" name="formhash" value="{FORMHASH}" />
<p class="tbmu">{lang lucas_watermark:my_watermark}</p>
<table cellspacing="0" cellpadding="0" class="tfm" style="table-layout:fixed;margin-top:10px;">
	<tbody>
		<tr>
	        <th class="mtm pns">
                {lang lucas_watermark:is_open}
            </th>
            <td class="mtm pns">
                <input id="watermark_y" class="radio" type="radio" name="iswater" value="1" {if $user['iswater'] == 1}checked="checked"{/if}><label for="watermark_y">{lang lucas_watermark:yes}</label>  
                <input id="watermark_n" class="radio" type="radio" name="iswater" value="0" {if $user['iswater'] == 0}checked="checked"{/if}><label for="watermark_n">{lang lucas_watermark:no}</label>
		   </td>
	    </tr>
	    <tr>
            <th class="mtm pns">
                {lang lucas_watermark:local}
           </th>
           <td class="mtm pns">
                <input id="watermark_l_1" class="radio" type="radio" name="local" value="0" {if $user['local'] == 0}checked="checked"{/if}><label for="watermark_l_1">{lang lucas_watermark:ld}</label>
                <input id="watermark_l_2" class="radio" type="radio" name="local" value="1" {if $user['local'] == 1}checked="checked"{/if}><label for="watermark_l_2">{lang lucas_watermark:rd}</label>
                <input id="watermark_l_3" class="radio" type="radio" name="local" value="2" {if $user['local'] == 2}checked="checked"{/if}><label for="watermark_l_3">{lang lucas_watermark:mi}</label>
                <input id="watermark_l_4" class="radio" type="radio" name="local" value="3" {if $user['local'] == 3}checked="checked"{/if}><label for="watermark_l_4">{lang lucas_watermark:lu}</label>
                <input id="watermark_l_5" class="radio" type="radio" name="local" value="4" {if $user['local'] == 4}checked="checked"{/if}><label for="watermark_l_5">{lang lucas_watermark:ru}</label>
           </td>
        </tr>
        <tr>
            <th class="mtm pns">
                {lang lucas_watermark:text}
            </th>
            <td class="mtm pns">
                <input type="text" name="text" class="px" value="{$user['text']}"><br />{lang lucas_watermark:tip_text}
             </td>
        </tr>
        </tr>
        <tr>
            <th class="mtm pns">
                {lang lucas_watermark:ttf}
            </th>
            <td class="mtm pns">
            		{if $ttflist == ''}
            			{lang lucas_watermark:ttf_notice}
            		{else}
                	<select name="ttf" >
                		{loop $ttflist $v}
					   <option value="{$v['unique']}" {if $user['ttf'] == $v['unique']}selected="selected"{/if}>{$v['name']}</option> 
					   {/loop}
				  </select>  
				 	 {/if}
             </td>
        </tr>
        <tr>
            <th class="mtm pns">
                {lang lucas_watermark:color}
            </th>
            <td class="mtm pns">
                <input id="c1_v" type="text" class="txt" style="float:left; width:210px;" value="{$user['color']}" name="color" onchange="updatecolorpreview('c1')">
                <input id="c1" onclick="c1_frame.location='static/image/admincp/getcolor.htm?c1|c1_v';showMenu({'ctrlid':'c1'})" type="button" class="colorwd" value="" style="background: {$user['color']}">
                <span id="c1_menu" style="display: none"><iframe name="c1_frame" src="" frameborder="0" width="210" height="148" scrolling="no"></iframe></span>
             </td>
        </tr>
        <tr>
            <th class="mtm pns">
                {lang lucas_watermark:size}
            </th>
            <td class="mtm pns">
                <select name="size">
				    <option {if $user['size'] == 10}selected="selected"{/if} value="10">10</option>
				    <option {if $user['size'] == 12}selected="selected"{/if} value="12">12</option>
				    <option {if $user['size'] == 14}selected="selected"{/if} value="14">14</option>
				    <option {if $user['size'] == 16}selected="selected"{/if} value="16">16</option>
				</select>
             </td>
        </tr>
        <tr>
            <th class="mtm pns">
                {lang lucas_watermark:file}
            </th>
            <td class="mtm pns">
                <input type="file" name="file" class="pf" size="25"><br />{lang lucas_watermark:tip_file}
             </td>
        </tr>
        <tr>
            <th class="mtm pns">
                {lang lucas_watermark:my_icon}
            </th>
            <td class="mtm pns">
                <img src="{$icon_file}?{TIMESTAMP}" style="width:50px;"/><br /><input type="checkbox" name="del" />{lang lucas_watermark:del}<br />
                <input type="hidden" name="file_path" value="{$icon_file}" />
             </td>
        </tr>
        <tr>
            <th class="mtm pns">
                {lang lucas_watermark:is_round}
            </th>
            <td class="mtm pns">
                <input id="watermark_ry" class="radio" type="radio" name="isround" value="1" {if $user['isround'] == 1}checked="checked"{/if}><label for="watermark_ry">{lang lucas_watermark:yes}</label>  
                <input id="watermark_rn" class="radio" type="radio" name="isround" value="0" {if $user['isround'] == 0}checked="checked"{/if}><label for="watermark_rn">{lang lucas_watermark:no}</label>
           </td>
        </tr>
        <tr>
			<td colspan="2">
			    <button onclick="showWindow('preview', 'plugin.php?id=lucas_watermark:preview', 'get', 1)" type="botton" name="preview" value="true" class="pn pnc"><strong>{lang lucas_watermark:preview}</strong></button>
				<button type="submit" name="submitwater" value="true" class="pn pnc"><strong>{lang lucas_watermark:save}</strong></button>
				<span id="submit_result" class="rq"></span>
			</td>
		</tr>
	</tbody>
</table>
</form>