/* 
xpath: /html/body/table/tbody/tr/td[1]/div/a
CSS:   html body table tbody tr td div#link1 a
*/
/*
link = $('a.baz_anchor:contains("baz")').parent().parent().parent().children().children('div#link1').children('a').attr('href');
alert(link);
*/
link = $('a.baz_anchor:contains("baz")').closest('tr').find('div#link1 a').attr('href');
alert(link);
