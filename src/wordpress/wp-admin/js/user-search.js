jQuery(function(a){var b=typeof(current_site_id)!="undefined"?"&site_id="+current_site_id:"";a("#adduser-email, #newuser").autocomplete({source:ajaxurl+"?action=autocomplete-user&autocomplete_type=add"+b,delay:500,minLength:2,open:function(d,c){a(this).addClass("open")},close:function(d,c){a(this).removeClass("open")}});a("#user-search-input").autocomplete({source:ajaxurl+"?action=autocomplete-user&autocomplete_type=search"+b,delay:500,minLength:2,open:function(d,c){a(this).addClass("open")},close:function(d,c){a(this).removeClass("open")}});a("#all-user-search-input").autocomplete({source:ajaxurl+"?action=autocomplete-user&autocomplete_type=search-all"+b,delay:500,minLength:2,open:function(d,c){a(this).addClass("open")},close:function(d,c){a(this).removeClass("open")}})});