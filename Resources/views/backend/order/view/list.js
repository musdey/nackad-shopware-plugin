//{namespace name="backend/order/main"}
//{block name="backend/order/view/list/list" append}
Ext.define('Shopware.apps.NackadPlugin.view.list.List', {
    override: 'Shopware.apps.Order.view.list.List',

    /**
     * Creates the grid columns
     *
     * @return [array] grid columns
     */
    getColumns:function () {
        var me = this;
        a
        const columns = me.callParent()

        columns.splice(9, 0, {
            header: 'Liefertag',
            dataIndex: 'shipping.zipCode',
            flex: 2,
            renderer:  function(value, metaData, record, colIndex, store, view) {
                var me = this,
                    name = '',
                    shipping = record.getShipping(),
                    comments = [];


                if (shipping instanceof Ext.data.Store && shipping.first() instanceof Ext.data.Model) {
                    shipping = shipping.first();

                    return shipping.get('zipCode')
                }

            }

        })

        return columns;
    },
});

//{/block}