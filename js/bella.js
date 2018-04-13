jQuery(document).ready(function ($) {
    $('.filter-term').on('change',function(){
        var value = $(this).val().match(new RegExp("\\bvalue-(.*)\\b"));
        if(value===null){
            return;
        }
        var tax = value[1].split('-')[0];
        value = value[1];
        var $redirect_node = $(this).parents('.redirect-url').eq(0);
        if($redirect_node.length===0){
            return;
        }
        var redirect = $redirect_node[0].className.match(new RegExp("\\bvalue-(.*)\\b"));
        if(redirect===null){
            return;
        }
        redirect = redirect[1];
        var redirect_query = redirect.match(new RegExp("\\?[^#]*(filter=([^&#]*))"));
        var current_page_query = window.location.href.match(new RegExp("\\?[^#]*(filter=([^&#]*))"));
        var filters = current_page_query ? 
            current_page_query[2]===""? Array():current_page_query[2].replace("%2C",",").split(",") :
            Array();
        var index = -1;
        for( var i = 0; i< filters.length; i++){
            if(filters[i].indexOf(tax)!==-1){
                index = i;
                break;
            }
        }
        filters.push(value);
        if(index !== -1){
            filters.splice(index,1);
        }
        if(filters.length>0){
            filters = filters.join(",");
        } else {
            filters = "";
        }
        if(redirect_query===null){
            var index = redirect.indexOf("?");
            if(index===-1){
                var index = redirect.indexOf("#");
                if(index===-1){
                    var full_url = redirect+"?filter="+filters;
                } else {
                    var full_url = redirect.slice(0,index)+"?filter="+filters+redirect.slice(index);
                }
            } else {
                var length = redirect.length;
                var full_url = redirect.slice(0,index+1)+"filter="+filters;
                if(index===length-1){
                    full_url = full_url + redirect.slice(index+1);
                } else {
                    full_url = full_url + "&"+redirect.slice(index+1);
                }
            }
        } else {
            var filter_string = redirect_query[1];
            var full_url = redirect.replace(filter_string,"filter="+filters);
        }
        window.location.href = full_url;
    });
    $('.bella-clear').click(function(){
        window.location.href = window.location.protocol+"//"+window.location.host+"/"+window.location.pathname;
    });
});