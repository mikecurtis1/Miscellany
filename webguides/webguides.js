var buildUrl = function(sql) {
	var baseUrl = 'http://spreadsheets.google.com/tq?key=0ApaJweMK6GrZdEFvSmU5SWc5RmRWZG5MdHh4bFlHNGc&tq=';
	sql = 'SELECT+*+WHERE' + sql.substring(3);
	return baseUrl + sql;
}

var processPage = function() {
	var sql = '';
	$('div#webguides div').each(
		function(i) {
			var index = i;
			/*var classList = $(this).attr('class').split(/ /);
			var id = classList[0];
			var idnum = classList[0].substring(2);
			var css = classList[1];*/
			var wgid = $(this).attr('wgid');
			var wgidNum = wgid.substring(2);
			var css = $(this).attr('class');
			sql = sql + '+OR+A+%3D+' + idnum;
		}
	);
	return sql;
}

var appendHtml = function(val) {
	var id = val.gsx$id.$t;
	var title = val.gsx$title.$t;
	var mime = val.gsx$mime.$t;
	var url = val.gsx$url.$t;
	var description = val.gsx$description.$t;
	var authorship = val.gsx$authorship.$t;
	var html = '';
	//$('div#webguides *.id' + id).each(
	// $('input[value="Hot Fuzz"]').next()
	//alert(id);
	$('div#webguides *[wgid="id'+id+'"]').each(
		//alert(id);
		function(i) {
			/*var classList = $(this).attr('class').split(/ /);
			var id = classList[0];
			var css = classList[1];*/
			var wgid = $(this).attr('wgid');
			var css = $(this).attr('class');
			//alert(id+'|'+wgid+'|'+css);
			if ( css == 'image' && mime.substring(0,5) == 'image' ) {
				var briefDescription = description.substring(0,120) + '... ';
				html = '<a href="'+url+'"><img src="'+url+'" alt="'+title+'. '+description+'" /></a><div class="title">'+title+'</div><div class="description">'+briefDescription+'</div>';
				$('*[wgid="id'+id+'"]').append(html + '<div class="debug">' + 'ID:' + wgid + ', CSS:' + css + '</div>');
			} else if ( css == 'link' ) {
				html = '<a href="'+url+'" title="'+description+'">'+title+'</a>';
				$('*[wgid="id'+id+'"]').replaceWith('<li>' + html + '<span class="debug">' + 'ID:' + wgid + ', CSS:' + css + '</span></li>');
			} else if ( css == 'pdf' ) {
				html = '<img src="pdf_icon.gif" class="icon" /><div class="title"><a href="'+url+'">'+title+'</a></div><div class="description">'+description+'</div>';
				$('*[wgid="id'+id+'"]').append(html + '<div class="debug">' + 'ID:' + wgid + ', CSS:' + css + '</div>');
			} else if ( css == 'text' && url == 0) {
				//TODO: use wiki markup or something like that in spreadsheet description, then translate allowed tags: h3, p, ul/ol, li, bold, em
				$('*[wgid="id'+id+'"]').append(description + '<div class="debug">' + 'ID:' + wgid + ', CSS:' + css + '</div>');
			} else if ( description.substring(0,3) == 'YQL' && mime == 'text/javascript' ) {
				var sourceUrl = description.substring(4);
				$.ajax({
					url : url, 
					type : 'jsonp', 
					dataType: 'jsonp', 
					success : function(data) {
						var text = data.query.results['result'];
						$('*[wgid="id'+id+'"]').append('<h3 class="title">' + title + '</h3><div class="snippet">' + text + '</div>');
						$('*[wgid="id'+id+'"] a').removeAttr('href');
						$('*[wgid="id'+id+'"]').append('<div class="source_url">Source: <a href="' + sourceUrl + '">'+authorship+'</a></div>');
					}
				});
			} else {
				html = '<div class="title"><a href="'+url+'">'+title+'</a></div><div class="description">'+description+'</div>';
				$('*[wgid="id'+id+'"]').append(html + '<div class="debug">' + 'ID:' + wgid + ', CSS:' + css + '</div>');
			}
			//$('*[wgid="id'+id+'"]').append(html + '<div class="debug">' + 'ID:' + wgid + ', CSS:' + css + '</div>');
		}
	);
}

var getAtomFeed = function(url) {
	// https://docs.google.com/spreadsheet/ccc?key=0ApaJweMK6GrZdEFvSmU5SWc5RmRWZG5MdHh4bFlHNGc
	var url = 'https://spreadsheets.google.com/feeds/list/0ApaJweMK6GrZdEFvSmU5SWc5RmRWZG5MdHh4bFlHNGc/od6/public/values?alt=json';
	$.getJSON(url, function(data) {
		$.each(data.feed.entry, function(key,val) {
			var id = val.gsx$id.$t;
			var title = val.gsx$title.$t;
			var mime = val.gsx$mime.$t;
			var url = val.gsx$url.$t;
			var description = val.gsx$description.$t;
			appendHtml(val);
		});
	});
}

//TODO: clean this up, try passing json data object to appendHtml, don't iterate each data, but iterate each div.id and pull the given id from data object
$(document).ready(function() {
	//var sql = processPage();
	//var url = buildUrl(sql);
	//getAtomFeed(url);
	getAtomFeed('');
});
