en4.book = {
	onCreateEditBook : function() {
		var isForeignerEle = $('is_foreign');
		if (isForeignerEle) {
			var showHideTranslator = function(checked) {
				if (checked) {
					$('translators-wrapper').setStyle('display', 'block');
				} else {
					$('translators-wrapper').setStyle('display', 'none');
				}
			}
			isForeignerEle.addEvent('click', function(event) {
				showHideTranslator(event.target.checked);								
			});
			showHideTranslator(isForeignerEle.checked);
		}	
	},
	
	changeCategory : function(element, name, model, route, isSearch, isFirst) {		
		var value = element.value;
		var e = element.name;
		var prefix = 'id_wrapper_' + name + '_';
		var level = element.name.replace(name + '_', '');
		level = parseInt(level);

		if(value == undefined || value == null || value == '') {
			if(level > 0) {
				var pre = name + '_' + (level - 1).toString();
				pre = document.getElementById(pre);
				if(pre != null && pre != undefined && pre) {
					value = $(pre).value;
				}
			}
		}
		element.form[name].value = value;

		if(isFirst == 0 && isSearch) {
			element.form.submit();
		}

		var ne = $(prefix + (level + 1));
		if(name == 'location_id') {
			var max = 3;
		} else {
			var max = 9;
		}
		for( i = level; i < max; i++) {
			if((document.getElementById(prefix + (i + 1)))) {
				document.getElementById(prefix + (i + 1)).style['display'] = 'none';
			}
		}
		
		var request = new Request({
			'url' : en4.core.baseUrl + route + '/multi-level/change',
			'data' : {
				'format' : 'html',
				'id' : element.value,
				'name' : name,
				'level' : level,
				'model' : model,
				'isSearch' : isSearch
			},
			'onComplete' : function(a) {
				if(a != null && a != undefined && a != '') {
					ne.setStyles({
						'margin-top' : '8px'
					});
					ne.setStyles({
						'display' : 'block'
					});
					ne.innerHTML = a;
				}
			}
		});
		request.send();
	},
		
	rate : function(guid, data) {
		var pre_rate = Number.from(data['rating']);
		var rated = data['rated'];
		var total_votes = data['total_votes'];
		var viewer = data['viewer'];
		var url = data['url'];
				
		var rating_over = function(rating) {
			if( rated == 1 ) {
				$(guid + '_rating_text').set('text', en4.core.language.translate('you already rated'));
			} else if( viewer == 0 ) {
				$(guid + '_rating_text').set('text', en4.core.language.translate('please log in to rate'));
			} else {
				$(guid + '_rating_text').set('text', en4.core.language.translate('click to rate'));
				for(var x = 1; x <= 5; x++) {
					if(x <= rating) {
						$(guid + '_rate_' + x).set('class', 'book_rating_star_big_generic book_rating_star_big');
					} else {
						$(guid + '_rate_' + x).set('class', 'book_rating_star_big_generic book_rating_star_big_disabled');
					}
				}
			}
		}
		
		var rating_out = function() {
			$(guid + '_rating_text').set('text', en4.core.language.translate(Array('%s rating', '%s ratings', total_votes), total_votes));
			if (pre_rate != 0){
				set_rating();
			} else {
				for(var x = 1; x <= 5; x++) {
					$(guid + '_rate_' + x).set('class', 
						'book_rating_star_big_generic book_rating_star_big_disabled');
				}
			}
		}
		
		var set_rating = function() {
			var rating = pre_rate;
			
			$(guid + '_rating_text').set('text', en4.core.language.translate(Array('%s rating', '%s ratings', total_votes), total_votes));
			for (var x = 1; x <= parseInt(rating); x++) {
				$(guid + '_rate_' + x).set('class', 'book_rating_star_big_generic book_rating_star_big');
			}

			for (var x = parseInt(rating) + 1; x <= 5; x++) {
				$(guid + '_rate_' + x).set('class', 'book_rating_star_big_generic book_rating_star_big_disabled');
			}
			
			var remainder = Math.round(rating) - rating;
			if (remainder <= 0.5 && remainder != 0){
				var last = parseInt(rating) + 1;
				$(guid + '_rate_' + last).set('class', 'book_rating_star_big_generic book_rating_star_big_half');
			}
		}
		
		var rate = function(rating) {
			for (var x = 1; x <= 5; x++) {
				$(guid + '_rate_' + x).set('onclick', '');
			}
			(new Request.JSON({
				'url' : url,
				'data' : {
					'format' : 'json',
					'rating' : rating
				},
				onSuccess : function(responseJSON, responseText) {
					$(guid + '_rating_text').set('text', en4.core.language.translate('Thanks for rating'));
					rated = 1;
					total_votes = Number.from(total_votes) + 1;
					pre_rate = (pre_rate + rating)/total_votes;
					set_rating();
					
					$(guid + '_rating_text').set('text', en4.core.language.translate(Array('%s rating', '%s ratings', total_votes), total_votes));
				}
			})).send();
		}
		
		set_rating();
		
		$(guid + '_rating').removeEvent('mouseout').addEvent('mouseout', function(event) {
			rating_out();
		});
		
		$(guid + '_rating').getChildren('span').each(function(item, index, object){
			$(item).removeEvent('mouseover').addEvent('mouseover', function(event) {				
				rating_over(index + 1);
			});
			if (!rated && viewer) {
				$(item).removeEvent('click').addEvent('click', function(event) {
					// TODO [DangTH] : check this issue again, logically, 
					// no need to check rated here, just remove the event click, but it doesn't work
					if (!rated) {
						rate(index + 1);
					}
				});
			}
		});
	}
};