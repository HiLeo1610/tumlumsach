<script type="text/javascript">
	var authors = [];
	var translators = [];
	var isPopulated = false;

  	<?php if( !empty($this->isPopulated) && !empty($this->toObjects) ): ?>
	    isPopulated = true;
	    <?php foreach($this->toObjects as $toObject) : ?>
	    	<?php if ($toObject instanceof User_Model_User) : ?>
			    to = {
			      	id : <?php echo sprintf("%d", $toObject->getIdentity()) ?>,
			      	type : '<?php echo $toObject->getType() ?>',
			      	guid : '<?php echo $toObject->getGuid() ?>',
			      	title : '<?php echo $this->string()->escapeJavascript($toObject->getTitle()) ?>'
			    };
			    authors.push(to);
		    <?php endif; ?>
	    <?php endforeach; ?>
  	<?php endif; ?>

  	function removeFromToValue(id) {
	    // code to change the values in the hidden field to have updated values
	    // when recipients are removed.
	    var toValues = $('toValues').value;
	    var toValueArray = toValues.split(",");
	    var toValueIndex = "";
	
	    var checkMulti = String.from(id).search(/,/);
	
	    // check if we are removing multiple recipients
	    if (checkMulti!=-1){
	    	var recipientsArray = id.split(",");
	      	for (var i = 0; i < recipientsArray.length; i++){
	        	removeToValue(recipientsArray[i], toValueArray);
	      	}
	    }
	    else{
	      	removeToValue(id, toValueArray);
	    }
	
	    // hide the wrapper for usernames if it is empty
	    if ($('toValues').value==""){
	      	$('toValues-wrapper').setStyle('height', '0');
	    }
	
	    $('toValues').disabled = false;
  	}

  	function removeToValue(id, toValueArray){
	    for (var i = 0; i < toValueArray.length; i++){
			if (toValueArray[i]==id) toValueIndex =i;
	    }
	
	    toValueArray.splice(toValueIndex, 1);
	    $('toValues').value = toValueArray.join();
  	}

  	en4.core.runonce.add(function() {
  		if( isPopulated ) { 
  			for (var i = 0; i < authors.length; i++) {
      			var to = authors[i];
		      	var myElement = new Element("span", {
			        'id' : 'tospan' + to.id,
			        'class' : 'tag tag_' + to.type,
			        'html' :  to.title  + ' <a href="javascript:void(0);" ' +
			                  'onclick="this.parentNode.destroy();removeFromToValue(' + to.id + ');">x</a>' 
	      		});
				$('toValues-element').appendChild(myElement);
			}
		    $('toValues-wrapper').setStyle('height', 'auto');
		    
		    for (var i = 0; i < translators.length; i++) {
      			var to = translators[i];
		      	var myElement = new Element("span", {
			        'id' : 'tospan' + to.id,
			        'class' : 'tag tag_' + to.type,
			        'html' :  to.title  + ' <a href="javascript:void(0);" ' +
			                  'onclick="this.parentNode.destroy();removeFromToValue(' + to.id + ');">x</a>' 
	      		});
				$('toTranslatorValues-element').appendChild(myElement);
			}
		    $('toTranslatorValues-wrapper').setStyle('height', 'auto');
		} else {
			// hide the wrapper for authors if it is empty
		    // if ($('toValues').value==""){
		    if (authors.length == 0){	
		      	$('toValues-wrapper').setStyle('height', '0');
		    }
		
		    $('toValues').disabled = false;
		    
		    // hide the wrapper for translators if it is empty
		    //if ($('toTranslatorValues').value==""){
		    if (translators.length == 0){	
		      	$('toTranslatorValues-wrapper').setStyle('height', '0');
		    }
		
		    $('toTranslatorValues').disabled = false;
		}
		
		Autocompleter.prototype.onCommand = function(e) {
  			if (!e && this.focussed)
				return this.prefetch();
			if (e && e.key && !e.shift && !this.options.ignoreKeys) {
				switch (e.key) {
					case 'enter':
						e.stop();
						if (!this.selected) {
							if (!this.options.customChoices) {
								// @todo support multiple
								this.element.value = '';
							}
							return true;
						}
						if (this.selected && this.visible) {
							this.choiceSelect(this.selected);
							return !!(this.options.autoSubmit);
						}
						break;
					case 'up':
					case 'down':
						var value = this.element.value;
						if (!this.prefetch() && this.queryValue !== null) {
							var up = (e.key == 'up');
							this.choiceOver((this.selected || this.choices)[
							(this.selected) ? ((up) ? 'getPrevious' : 'getNext') : ((up) ? 'getLast' : 'getFirst')
							](this.options.choicesMatch), true);
							this.element.value = value;
						}
						return false;
					case 'esc':
						this.hideChoices(true);
						if (!this.options.customChoices)
							this.element.value = '';
						//if (this.options.autocompleteType=='message') this.element.value="";
						break;
					case 'tab':
						if (this.selected && this.visible) {
							this.choiceSelect(this.selected);
							return !!(this.options.autoSubmit);
						} else {
							this.hideChoices(true);
							// if (!this.options.customChoices)
								// this.element.value = '';
							//if (this.options.autocompleteType=='message') this.element.value="";
							break;
						}
	
				}
			}
			this.fireEvent('onCommand', e);
			return true;
  		}
		
  		new Autocompleter.Request.JSON('authors', '<?php echo $this->url(array('action' => 'suggest', 'format' => 'json'), 'author_general', true) ?>', {
	        'minLength': 2,
	        'delay' : 250,
	        'selectMode': 'pick',
	        'autocompleteType': 'message',
	        'multiple': false,
	        'className': 'message-autosuggest',
	        'filterSubset' : true,
	        'tokenFormat' : 'object',
	        'tokenValueKey' : 'label',
	        'customChoices' : '',
	        'hiddenElementId' : 'toValues',
	        'injectChoice': function(token){
	        	var toValues = $('toValues').value;
	        	var toValueArray = toValues.split(",");
	        	var ck = true;
	        	for (var i = 0; i < toValueArray.length; i++) {
	        		if (toValueArray[i] == token.id) {
	        			ck = false;
	        			break;
	        		}
	        	}
	        	if (ck) {
		            var choice = new Element('li', {
		              'class': 'autocompleter-choices',
		              'html': token.photo,
		              'id':token.label
		            });
		            new Element('div', {
		              'html': this.markQueryValue(token.label),
		              'class': 'autocompleter-choice'
		            }).inject(choice);
		            this.addChoiceEvents(choice).inject(this.choices);
		            choice.store('autocompleteChoice', token);
	            }
	        }        
  		});
  		
  		new Autocompleter.Request.JSON('translators', '<?php echo $this->url(array('action' => 'suggest', 'format' => 'json'), 'author_general', true) ?>', {
	        'minLength': 2,
	        'delay' : 250,
	        'selectMode': 'pick',
	        'autocompleteType': 'message',
	        'multiple': false,
	        'className': 'message-autosuggest',
	        'filterSubset' : true,
	        'tokenFormat' : 'object',
	        'tokenValueKey' : 'label',
	        'hiddenElementId' : 'toTranslatorValues',
	        'injectChoice': function(token){
	        	var toValues = $('toTranslatorValues').value;
	        	var toValueArray = toValues.split(",");
	        	var ck = true;
	        	for (var i = 0; i < toValueArray.length; i++) {
	        		if (toValueArray[i] == token.id) {
	        			ck = false;
	        			break;
	        		}
	        	}
	        	if (ck) {
		            var choice = new Element('li', {
		              'class': 'autocompleter-choices',
		              'html': token.photo,
		              'id':token.label
		            });
		            new Element('div', {
		              'html': this.markQueryValue(token.label),
		              'class': 'autocompleter-choice'
		            }).inject(choice);
		            this.addChoiceEvents(choice).inject(this.choices);
		            choice.store('autocompleteChoice', token);
	            }
	        }        
  		});
  	});
</script>