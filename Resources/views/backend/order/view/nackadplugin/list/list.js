//{block name="backend/order/view/list/list" append}
//{namespace name="backend/meinplugin/main"}
Ext.define('Shopware.apps.Order.view.nackadplugin.list.List', {

    /**
     * Defines an override applied to a class.
     * @string
     */
    override: 'Shopware.apps.Order.view.list.List',

    /**
     * Overrides the getColumns function of the overridden ExtJs object
     * and inserts two new columns
     * @return
     */
    getColumns: function() {
        var me = this;

        var columns = me.callOverridden(arguments);

        var columnDate = {
            header: 'Lieferslot',
            dataIndex:'delivery_day',
            flex: 2,
            sortable: true
        };

        return Ext.Array.insert(columns, 1, [columnDate]);
    }

});
//{/block}