<{if  $access == 1}>
<ul id="smallworld_menu" class="smallworld_menu">
    <li class="active">
        <a href="<{$xoops_url}>/modules/smallworld/index.php"><{$smarty.const._SMALLWORLD_HOME}></a>
    </li>
</ul>
<div id="example1" class="smallworld_content">
    <table class="smallworld_biotable">
        <tr>
            <td>
                <{if $xoops_isuser && $check >= 2}>
                <{$avatar}>
                <div class="UploadNewAvatar" id="<{$ownerofpage}>" style="display:none">
                    <div id="smallworld_avatarupload">
                        <span><{$smarty.const._SMALLWORLD_UPLOADFILEBUTTONTEXT}></span>
                    </div>
                    <span id="smallworld_avatarstatus"></span>
                    <ul id="smallworld_avatarfiles"></ul>
                </div>
                <br>
                <{if $isadminuser == 'YES' || $username == $myusername}>
                <br>
                <p class="smallworld_useredits_menu">
                    <img height="10px" width="10px" src="assets/images/editavatar.png">
                    <a href="javascript:void(0);" id="smallworld_changeAvatar"><{$smarty.const._SMALLWORLD_CHANGEAVATAR}></a><br>
                    <img height="10px" width="10px" src="assets/images/edituserprofile.png">
                    <a href="editprofile.php" id="smallworld_changeEditProfile"><{$smarty.const._SMALLWORLD_EDITPROFILE}></a><br>
                    <img height="10px" width="10px" src="assets/images/preferences.png">
                    <a href="javascript:void(0);" id="smallworld_changePersSettings"><{$smarty.const._SMALLWORLD_MENU_PRIVSET}></a>
                    <br>
                </p>
                <{else}>
                <div></div>
                <{/if}>

                <br>
                <p class="smallworld_useredits_menu">
                    <img width='10px' height='10px' src="<{$xoops_url}>/modules/smallworld/assets/images/statistics.png">
                    <a href="javascript:void(0);" id="smallworld_statistics_lnk"><{$smarty.const._SMALLWORLD_STATS}></a><br>
                </p>
                <br>

            </td>
            <{else}>
            <td>
                <br>
            </td>
            <{/if}>
            <span class="smallworld_search">
                         <td>
                            <ul class="smallworld_index_headmenu">
                                <li><{$menu_home}></li>
                                <li><{$menu_profile}></li>
                                <li><{$menu_friends}></li>
                                <li><{$menu_gallery}></li>
                                <li><{$menu_register}></li>
                            </ul>
                            <br>
                            <br>

                            <div style="text-align: center;"><input id="smallworld_searchform" size="35" type="text" value="<{$smarty.const._SMALLWORLD_SEARCHMEMBER}>"></div>
                            <br>
                            <br>
                                    <div id="smallworld_wall_container">
                                        <div id="smallworld_updateboxarea">
                                            <h4><{$smarty.const._SMALLWORLD_WHATSUP}></h4>
                                            <form method="post" action="">
                                                <{if $xoops_isuser}>
                                                <textarea cols="50" rows="1" rel="<{$ownerofpage}>" name="smallworld_update" id="smallworld_update"></textarea>
                                                <{else}>
                                                <textarea cols="50" rows="1"></textarea>
                                                <{/if}>
                                                <br>
                                                <div id="smallworld_updatePrivacyCheck">
                                                    <label for="updatePriv"><{$smarty.const._SMALLWORLD_PRIVATEUPDATE}></label>
                                                    <input type="radio" name="updatePublic" id="updatePublic" value="1">
                                                    <label for="updatePriv"><{$smarty.const._SMALLWORLD_PUBLICUPDATE}></label>
                                                    <input type="radio" name="updatePublic" id="updatePublic" value="0">

                                                </div>
                                                <br><br>
                                                <{if $xoopsUser}>
                                                <input type="submit" value="<{$smarty.const._SMALLWORLD_UPDATEBUTTONTEXT}>" id="smallworld_update_button" class="smallworld_update_button">
                                                <{else}>
                                                <input type="submit" value="<{$smarty.const._SMALLWORLD_UPDATEBUTTONTEXT}>" id="smallworld_update_button" class="smallworld_update_button">
                                                <{/if}>
                                            </form>
                                        </div>
                                        <hr>
                                        <div id="smallworld_Stats_container"></div>
                                        <button id='smallworld_messagecounter_id' onClick='smallworldRefresh();return false;'></button>
                                        <div id='smallworld_flashmessage'>
                                            <div id="smallworld_flash" align="left"></div>
                                        </div>
                                    <div id="smallworld_content">
                                     <{foreach item=post from=$walldata}>
                                    <{php}>
                                        if ( count($post.msg_id) < 1 ) {
                                            echo _SMALLWORLD_EMPTYMSG;
                                        }
                                        
                                    <{/php}>
                                    <div class="smallworld_stbody" id="smallworld_stbody<{$post.msg_id}>">
                                        <script type="text/javascript">
                                                xoops_smallworld(document).ready(function () {
                                                    xoops_smallworld('#smallworld_stexpand<{$post.msg_id}>').oembed('<{$post.orimessage}>', {
                                                        embedMethod: 'fill',
                                                        maxWidth: "100%",
                                                        maxHeight: "100%"
                                                    });
                                                });
                                        </script>
                                        <div class="smallworld_stimg">
                                            <img src="<{$post.avatar_link}>" class='smallworld_big_face' <{$post.avatar_highwide}>>
                                        </div>

                                    <div class="smallworld_sttext">
                                        <{if $isadminuser == 'YES' or $post.username == $myusername}>
                                            <a class="smallworld_stdelete" href="#" rel="<{$post.uid_fk}>" id="<{$post.msg_id}>" title="<{$smarty.const._SMALLWORLD_DELUPD}>">X</a>
                                        <{/if}>
                                         <a href="<{$post.permalink}>"> <img style="height:10px; width:10px" src="<{$post.linkimage}>"> </a>
                                        <b><a class="smallworld_wall_link" href="<{$xoops_url}>/modules/smallworld/userprofile.php?username=<{$post.username}>"><{$post.username}></a></b>
                                         <{$post.message}>
                                        <div class="smallworld_sttime"><{$post.created}><{$post.sharelink}> | <a href='#' class='smallworld_commentopen' id='<{$post.msg_id}>' title='<{$smarty.const._SMALLWORLD_COMMENTWALL}>'><{$smarty.const._SMALLWORLD_COMMENTWALL}></a></div>
                                        <{$post.sharediv}>
                                        <{if $post.username == $myusername}>
                                            <div class="smallworld_stcommentvote">
                                                <span id="smallworld_votenum"><{$post.vote_up}></span>
                                                    <img class="smallworld_voteimg" src="assets/images/like.png">

                                                <span id="smallworld_votenum"><{$post.vote_down}></span>
                                                    <img class="smallworld_voteimg" src="assets/images/dislike.png">
                                            </div>
                                        <{else}>
                                        <div class="smallworld_stcommentvote">
                                                <span id="smallworld_votenum"><{$post.vote_up}></span> <a href="javascript:void(0)" name="up" class="smallworld_stcomment_vote" id="<{$post.msg_id}>" type="msg" owner="<{$post.uid_fk}>">
                                                    <img class="smallworld_voteimg" src="assets/images/like.png">
                                                </a>
                                                <span id="smallworld_votenum"><{$post.vote_down}></span> <a href="javascript:void(0)" name="down" class="smallworld_stcomment_vote" id="<{$post.msg_id}>" type="msg" owner="<{$post.uid_fk}>">
                                                    <img class="smallworld_voteimg" src="assets/images/dislike.png">
                                                </a>
                                                <a href="javascript:void(0)" auserid="<{$post.uid_fk}>" by_user="<{$myusername}>" a_user="<{$post.username}>" name="complaint" class="smallworld_comment_complaint" id="<{$post.compl_msg_lnk}>">
                                                    <img class="smallworld_voteimg" src="assets/images/complaint.png">
                                                </a>
                                        </div>
                                        <{/if}>
                                        <div id="smallworld_stexpandbox">
                                            <div id="smallworld_stexpand<{$post.msg_id}>"></div>
                                        </div>

                                        <div class="smallworld_commentcontainer" id="smallworld_commentload<{$post.msg_id}>">

                                            <{section name=i loop=$comm}>
                                                <{if $comm[i].msg_id_fk == $post.msg_id}>
                                                <div class="smallworld_stcommentbody" id="smallworld_stcommentbody<{$comm[i].com_id}>">
                                                    <div class="smallworld_stcommentimg">
                                                    <img src="<{$comm[i].avatar_link}>" class='smallworld_small_face' <{$comm[i].avatar_highwide}>>
                                                    </div>
                                                    <div class="smallworld_stcommenttext">
                                                        <{if $isadminuser == 'YES' or $comm[i].username == $myusername}>
                                                        <a class="smallworld_stcommentdelete" href="#" rel="<{$comm[i].uid}>" id='<{$comm[i].com_id}>' title='<{$smarty.const._SMALLWORLD_DELETECOMMENT}>'>X</a>
                                                        <{/if}>
                                                        <div class="comm_holder">
                                                        <a class="smallworld_wall_link" href="<{$xoops_url}>/modules/smallworld/userprofile.php?username=<{$comm[i].username}>">
                                                            <b><{$comm[i].username}></b>
                                                        </a> <{$comm[i].comment}></div>
                                                            <div class="smallworld_stcommenttime"><{$comm[i].time}></div>
                                                             <{if $comm[i].username == $myusername}>
                                                                <div class="smallworld_stcommentvote">
                                                                    <span id="smallworld_votenum"><{$comm[i].vote_up}></span>
                                                                        <img class="smallworld_voteimg" src="assets/images/like.png">


                                                                    <span id="smallworld_votenum"><{$comm[i].vote_down}></span>
                                                                        <img class="smallworld_voteimg" src="assets/images/dislike.png">
                                                                </div>
                                                             <{else}>
                                                              <div class="smallworld_stcommentvote">
                                                                    <span id="smallworld_votenum"><{$comm[i].vote_up}></span> <a href="javascript:void(0)" name="up" class="smallworld_stcomment_vote" id="<{$comm[i].com_id}>" owner="<{$comm[i].uid}>" type="com" type2="<{$comm[i].msg_id_fk}>">
                                                                        <img class="smallworld_voteimg" src="assets/images/like.png">
                                                                    </a>

                                                                    <span id="smallworld_votenum"><{$comm[i].vote_down}></span> <a href="javascript:void(0)" name="down" class="smallworld_stcomment_vote" id="<{$comm[i].com_id}>" owner="<{$comm[i].uid}>" type="com" type2="<{$comm[i].msg_id_fk}>">
                                                                        <img class="smallworld_voteimg" src="assets/images/dislike.png">
                                                                    </a>
                                                                    <a href="javascript:void(0)" auserid="<{$comm[i].uid}>" by_user="<{$myusername}>" a_user="<{$comm[i].username}>" name="complaint" class="smallworld_comment_complaint" id="<{$comm[i].compl_msg_lnk}>">
                                                                        <img class="smallworld_voteimg" src="assets/images/complaint.png">
                                                                    </a>
                                                              </div>
                                                             <{/if}>
                                                    </div>
                                                </div>
                                                <{/if}>
                                            <{/section}>
                                        </div>

                                        <div class="smallworld_commentupdate" style="display:none" id="smallworld_commentbox<{$post.msg_id}>">
                                            <div class="smallworld_stcommentimg">
                                                <{if $xoopsUser}>
                                                <img src="<{$myavatarlink}>" class='smallworld_small_face' width="35px" height="35px">
                                                <{else}>
                                                <img src="<{$myavatarlink}>" class='smallworld_small_face' width="35px" height="35px">
                                                <{/if}>
                                            </div>
											
                                            <div class="smallworld_stcommenttext">
                                                <form method="post" action="">
                                                    <textarea name="smallworld_comment" class="smallworld_comment" id="smallworld_ctextarea<{$post.msg_id}>"></textarea>
                                                    <br>
                                                    <input type="submit" value="<{$smarty.const._SMALLWORLD_COMMENTBUTTONTEXT}>" id="<{$post.msg_id}>" class="smallworld_comment_button">
                                                </form>
                                            </div>
                                        </div>

                                    </div>

                                    </div>

                                     <{/foreach}>
                                    <div id="smallworld_moremsg_ajax" style="display:none">
                                        <img src="assets/images/loader.gif">
                                    </div>
                                    <a href="javascript:void(0)" class="smallworld_msg_counter" rel2="" rel="<{$pagename}>" id="<{$post.msg_id}>"><{$smarty.const._SMALLWORLD_MOREBUTTONLINK}></a>
                                        </div>
                                    </div>
                         </td>
                        </span>
        </tr>
    </table>
</div>


<{$usersetting}>
<div id="smallworldStatsDiv" title="<{$smarty.const._SMALLWORLD_STATS}>" style="display:none;"></div>
<{else}>
<{if $pagename != 'publicindex'}>
<div id="smallworld_notyetregistered" title="<{$smarty.const._SMALLWORLD_NOTYETREGISTERED_TITLE}>">
    <table border="0" class="smallworld_table" cellspacing="0" cellpadding="0">
        <tr>
            <label for="register"></label>
            <td><p id="smallworld_notyetusercontent"><{$smarty.const._SMALLWORLD_NOTYETUSER_BOXTEXT}></p></td>
        </tr>
    </table>
</div>
<{/if}>
<{/if}>
