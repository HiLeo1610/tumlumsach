/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
window.addEvent('domready', function() {
   $$('.book-category-sub-category').set('styles', {
        display : 'none'
    });
    
    $$('.book-category-collapse-control').addEvent('click', function(event) {
        var row = this.getParent('tr');
        var rowSubCategories = row.getAllNext('tr');
        if (this.hasClass('book-category-collapsed')) {  
            this.removeClass('book-category-collapsed');
            this.addClass('book-category-no-collapsed')
            for(var i = 0; i < rowSubCategories.length; i++) {
                if (!rowSubCategories[i].hasClass('book-category-sub-category')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'table-row'
                    });
                }
            }
        } else {
            this.removeClass('book-category-no-collapsed');
            this.addClass('book-category-collapsed');
            for(var i = 0; i < rowSubCategories.length; i++) {
                if (!rowSubCategories[i].hasClass('book-category-sub-category')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'none'
                    });
                }
            }
        }
    }); 
});