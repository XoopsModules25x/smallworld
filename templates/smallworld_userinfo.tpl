<style type="text/css">
    .smallworld_userinfo {
        width: 90%;
        padding: 5px;
    }

    .smallworld_userinfo h2 {
        font-size: 12px;
        margin: 5px 5px;
    }

    .smallworld_userinfo strong {
        font-weigh: 700;
    }

    .smallworld_userinfo img {
        position: relative;
        vertical-align: middle;
    }

    .smallworld_userinfo a {
        position: relative;
        vertical-align: middle;
    }

    .x-small {
        left: 5%;
        position: relative;
    }
</style>

<div class="smallworld_userinfo">
    <h2><{$smarty.const._SMALLWORLD_RECENTACTIVITY}></h2>
    <!-- start module search results loop -->
    <{if is_array($modules) && count($modules) > 0}>
    <{foreach item=module from=$modules}>

    <h4><{$module.name}></h4>

    <!-- start results item loop -->
    <{foreach item=result from=$module.results}>

    <img src="<{$result.image}>" alt="<{$module.name}>">
    <strong> <a href="<{$result.link}>" title="<{$result.title}>"><{$result.title}></a></strong><br><span class="x-small">(<{$result.time}>)</span><br>

    <{/foreach}>
    <!-- end results item loop -->
    <br>
    <{$module.showall_link}>
    <br><br>
    <{/foreach}>
    <{else}>
    <br><{$smarty.const._SMALLWORLD_ALL_FIELDS_DISABLED}>
    <{/if}>

    <!-- end module search results loop -->
</div>
