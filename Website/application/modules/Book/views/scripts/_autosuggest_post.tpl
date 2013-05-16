<script type="text/javascript">
	var taggedUsers = [];
	var taggedBooks = [];
	var parentBook = null;
	var isPopulated = false;

	var bookParentAutocompleter = null;
	var bookAutocompleter = null;
	var userAutocompleter = null;
	
	<?php if(!empty($this->isPopulated) 
		&& (!empty($this->toTaggedUsers) || !empty($this->toTaggedBooks) || !empty($this->parentBook))): ?>
	    isPopulated = true;
	    
	    <?php foreach($this->toTaggedUsers as $toTaggedUser) : ?>
		    toUser = {
		      	id : <?php echo sprintf("%d", $toTaggedUser->getIdentity()) ?>,
		      	type : '<?php echo $toTaggedUser->getType() ?>',
		      	guid : '<?php echo $toTaggedUser->getGuid() ?>',
		      	title : '<?php echo $this->string()->escapeJavascript($toTaggedUser->getTitle()) ?>'
		    };
		    taggedUsers.push(toUser);
	    <?php endforeach; ?>
	    
	    <?php foreach($this->toTaggedBooks as $toTaggedBook) : ?>
		    toBook = {
		      	id : <?php echo sprintf("%d", $toTaggedBook->getIdentity()) ?>,
		      	type : '<?php echo $toTaggedBook->getType() ?>',
		      	guid : '<?php echo $toTaggedBook->getGuid() ?>',
		      	title : '<?php echo $this->string()->escapeJavascript($toTaggedBook->getTitle()) ?>'
		    };
		    taggedBooks.push(toBook);
	    <?php endforeach; ?>

	    <?php if (!empty($this->parentBook)) : ?>
	       	parentBook = {
	    	    id : <?php echo sprintf("%d", $this->parentBook->getIdentity())?>,
				type : '<?php echo $this->parentBook->getType()?>',
				guid : '<?php echo $this->parentBook->getGuid()?>',
				title : '<?php echo $this->string()->escapeJavascript($this->parentBook->getTitle())?>'	    	    	    	
	    	}
	    <?php endif; ?>
  	<?php endif; ?>
  	
  	function removeFromToValue(id, eleId) {
	    // code to change the values in the hidden field to have updated values
	    // when recipients are removed.
	    var toValues = $(eleId).value;
	    var toValueArray = toValues.split(",");
	    var toValueIndex = "";
	
	    var checkMulti = String.from(id).search(/,/);
	
	    // check if we are removing multiple recipients
	    if (checkMulti!=-1){
	    	var recipientsArray = id.split(",");
	      	for (var i = 0; i < recipientsArray.length; i++){
	        	removeToValue(recipientsArray[i], toValueArray, eleId);
	      	}
	    }
	    else{
	      	removeToValue(id, toValueArray, eleId);
	    }
	
	    // hide the wrapper for usernames if it is empty
	    if ($(eleId).value==""){
	      	$(eleId + '-wrapper').setStyle('height', '0');
	    }
	
	    $(eleId).disabled = false;
  	}
  	
  	function removeToValue(id, toValueArray, eleId){
	    for (var i = 0; i < toValueArray.length; i++){
			if (toValueArray[i]==id) 
				toValueIndex = i;
	    }
	
	    toValueArray.splice(toValueIndex, 1);
	    $(eleId).value = toValueArray.join();
  	}
  	
  	function removeFromElement(ele, id, eleId) {
    	ele.parentNode.destroy();
    	removeFromToValue(id, eleId);
    }

  	function showInput(ele) {
      	ele.set('style', 'display:block');
      	ele.focus();
  	}
	
    en4.core.runonce.add(function() {
        var tagsUrl = '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>';

        var autocompleter = new Autocompleter.Request.JSON('tags', tagsUrl, {
            'postVar' : 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest',
            'customChoices' : true,
            'filterSubset' : true,
            'multiple' : true,
            'injectChoice': function(token){
                var choice = new Element('li', {
                	'class': 'autocompleter-choices', 
                	'value':token.label, 
                	'id':token.id
            	});
            	
                new Element('div', {
                	'html': this.markQueryValue(token.label),
                	'class': 'autocompleter-choice'
            	}).inject(choice);
            	
                choice.inputValue = token;
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });
        
        if (isPopulated) { 
  			for (var i = 0; i < taggedUsers.length; i++) {
      			var taggedUser = taggedUsers[i];
	      		
	      		 var myUserElement = new Element("span", {
			        'id' : 'tospan' + taggedUser.id,
			        'class' : 'tag tag_' + taggedUser.type,
			        'html' :  taggedUser.title + ' ' 
	      		});
	      		var myUserDeleteElement = new Element('a', {
	      			'href' : 'javascript:void(0)',
	      			'onclick' : 'removeFromElement(this, ' + taggedUser.id + ', "' + 'toValues' + '")',
	      			'html' : 'x'	
	      		});
	      		myUserDeleteElement.inject(myUserElement);
				$('toValues-element').appendChild(myUserElement);
			}
			<?php if (!empty($this->toTaggedUsers)) : ?>
		    	$('toValues-wrapper').setStyle('height', 'auto');
		    <?php else : ?>
		    	$('toValues-wrapper').setStyle('height', 0);	
			<?php endif ?>
		    
		    for (var i = 0; i < taggedBooks.length; i++) {
      			var taggedBook = taggedBooks[i];
	      		
	      		var myBookElement = new Element("span", {
			        'id' : 'tospan' + taggedBook.id,
			        'class' : 'tag tag_' + taggedBook.type,
			        'html' :  taggedBook.title + ' '  
	      		});
	      		var myBookDeleteElement = new Element('a', {
	      			'href' : 'javascript:void(0)',
	      			'onclick' : 'removeFromElement(this, ' + taggedBook.id + ', "' + 'toBookValues' + '")',
	      			'html' : 'x'	
	      		});
	      		myBookDeleteElement.inject(myBookElement);
				$('toBookValues-element').appendChild(myBookElement);
			}
			<?php if (!empty($this->toTaggedBooks)) : ?>
		    	$('toBookValues-wrapper').setStyle('height', 'auto');
		    <?php else : ?>
		    	$('toBookValues-wrapper').setStyle('height', 0);	
		    <?php endif; ?>
		    if (parentBook != null) {
		    	var parentBookElement = new Element("span", {
			        'id' : 'to_book_span' + parentBook.id,
			        'class' : 'tag tag_' + parentBook.type,
			        'html' :  parentBook.title + ' '  
	      		});
	      		var parentBookDeleteElement = new Element('a', {
	      			'href' : 'javascript:void(0)',
	      			'onclick' : 'removeFromElement(this, ' + parentBook.id + ', "' + 'parentBookValue' + '");showInput($("book"))',
	      			'html' : 'x'	
	      		});
	      		parentBookDeleteElement.inject(parentBookElement);
				$('book-element').appendChild(parentBookElement);
				$('book').set('style', 'display:none');
		    }
		} else {
			// hide the wrapper for tagged users if it is empty
		    if ($('toValues').value==""){
		      	$('toValues-wrapper').setStyle('height', '0');
		    }
		
		    $('toValues').disabled = false;
		    
		    // hide the wrapper for tagged users if it is empty
		    if ($('toBookValues').value==""){
		      	$('toBookValues-wrapper').setStyle('height', '0');
		    }
		
		    $('toBookValues').disabled = false;

		    if ($('parentBookValue').value == '') {
		    	$('parentBookValue-wrapper').setStyle('height', '0');
		    }
		}

        userAutocompleter = new Autocompleter.Request.JSON('tags_user', '<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest'), 'default', true) ?>', {
            'minLength': 2,
            'delay' : 250,
            'selectMode': 'pick',
            'autocompleteType': 'message',
            'multiple': false,
            'className': 'message-autosuggest',
            'filterSubset' : true,
            'tokenFormat' : 'object',
            'tokenValueKey' : 'label',
            'hiddenElementId' : 'toValues',
            'injectChoice': function(token){
            	if(token.type == 'user'){
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
	
		userAutocompleter.doAddValueToHidden = function(name, toID, hideLoc, newItem, list){
			// This is code for the invisible values
          	var hiddenInputField = document.getElementById(hideLoc);
          	var previousToValues = hiddenInputField.value;
			var values = previousToValues.split(',');
			if (values.contains(String.from(toID))) {
				return;
			}
          	if (userAutocompleter.checkSpanExists(name, toID)){
            	if (previousToValues==''){
              		document.getElementById(hideLoc).value = toID;
            	}
            	else {
              		document.getElementById(hideLoc).value = previousToValues+","+toID;
            	}
            	userAutocompleter.doPushSpan(name, toID, newItem, hideLoc, list);
          	}
		}
		
        bookAutocompleter = new Autocompleter.Request.JSON('tags_book', '<?php echo $this->url(array(
        		'module' => 'book',
        		'controller' => 'book',
        		'action' => 'suggest',
        		'parent_id' => $this->parent_id), 'default', true) ?>', {
            'minLength': 2,
            'delay' : 250,
            'selectMode': 'pick',
            'autocompleteType': 'message',
            'multiple': false,
            'className': 'message-autosuggest',
            'filterSubset' : true,
            'tokenFormat' : 'object',
            'tokenValueKey' : 'label',
            'hiddenElementId' : 'toBookValues',
            'injectChoice': function(token){
            	var choice = new Element('li', {
              		'class': 'autocompleter-choices friendlist',
              		'id':token.label
            	});
                new Element('div', {
					'html': this.markQueryValue(token.label),
                  	'class': 'autocompleter-choice'
                }).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
      	});
      	
      	bookAutocompleter.doAddValueToHidden = function(name, toID, hideLoc, newItem, list){
			// This is code for the invisible values
          	var hiddenInputField = document.getElementById(hideLoc);
          	var previousToValues = hiddenInputField.value;
			var values = previousToValues.split(',');
			if (values.contains(String.from(toID))) {
				return;
			}
          	if (bookAutocompleter.checkSpanExists(name, toID)){
            	if (previousToValues==''){
              		document.getElementById(hideLoc).value = toID;
            	}
            	else {
              		document.getElementById(hideLoc).value = previousToValues+","+toID;
            	}
            	bookAutocompleter.doPushSpan(name, toID, newItem, hideLoc, list);
          	}
		}

      	bookParentAutocompleter = new Autocompleter.Request.JSON('book', '<?php echo $this->url(array(
        		'module' => 'book',
        		'controller' => 'book',
        		'action' => 'suggest',
        		'parent_id' => $this->parent_id), 'default', true) ?>', {
            'minLength': 2,
            'delay' : 250,
            'selectMode': 'pick',
            'autocompleteType': 'message',
            'multiple': false,
            'className': 'message-autosuggest',
            'filterSubset' : true,
            'tokenFormat' : 'object',
            'tokenValueKey' : 'label',
            'hiddenElementId' : 'parentBookValue',
            'injectChoice': function(token){
            	var choice = new Element('li', {
              		'class': 'autocompleter-choices friendlist',
              		'id':token.label
            	});
                new Element('div', {
					'html': this.markQueryValue(token.label),
                  	'class': 'autocompleter-choice'
                }).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
      	});

      	bookParentAutocompleter.doPushSpan = function(name, toID, newItem, hideLoc, list) {
      		var myElement = new Element("span");
    		if (newItem) {
    			myElement.id = "tospan_" + name + "_" + toID;
    			myElement.innerHTML = name 
    				+ " <a href='javascript:void(0);' onclick='this.parentNode.destroy();showInput(bookParentAutocompleter.element);" 
    				+ "removeFromToValue(\"" + toID + "\", \"" + hideLoc + "\");'>x</a>";
    		} else {
    			myElement.id = "tospan_" + name + "_" + toID;
    			myElement.innerHTML = name 
    				+ " <a href='javascript:void(0);' onclick='this.parentNode.destroy();showInput(bookParentAutocompleter.element);" 
    				+ "removeFromToValue(\"" + toID + "\", \"" + hideLoc + "\");'>x</a>";
    		}
    		var loc = this.element.getParent();

    		if (list == null)
    			list = "";
    		myElement.addClass("tag" + list);

    		myElement.inject(loc);
    		this.element.set('style', 'display:none');
    		this.fireEvent('push');
      	}
    });
</script>