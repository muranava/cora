/* Class: POSTagset

   Class representing a POS tagset.
 */
var POSTagset = new Class({
    Extends: Tagset,
    Implements: SplitClassTagset,
    optgroup: null,

    initialize: function(data) {
        this.parent(data);
    },

    processTags: function(tags) {
        this.parent(tags);
        this.processSplitTags();
        this.optgroup = this.generateOptgroupFor(Object.keys(this.tags_for));
    },

    buildTemplate: function(td) {
        var select = td.getElement('select');
        if(typeof(select) === "undefined")
            return this;
        select.empty();
        if(this.processed)
            select.grab(this.optgroup.clone());
        return this;
    }
});
