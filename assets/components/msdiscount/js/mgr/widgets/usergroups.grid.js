msDiscount.grid.Users = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		id: 'msd-grid-users',
		url: msDiscount.config.connector_url,
		baseParams: {
			action: 'mgr/usergroups/getlist'
		},
		autosave: true,
		save_action: 'mgr/usergroups/updatefromgrid',
		//preventSaveRefresh: false,
		fields: ['id', 'name', 'discount', 'joinsum', 'rates', 'actions'],
		autoHeight: true,
		paging: true,
		remoteSort: true,
		columns: this.getColumns(config),
		tbar: [{
			text: (MODx.modx23
				? '<i class="icon icon-share"></i> '
				: '<i class="fa fa-share"></i> ')
			+ _('msd_btn_group_create'),
			handler: this.createGroup,
			scope: this
		}]
	});
	msDiscount.grid.Users.superclass.constructor.call(this, config);
};
Ext.extend(msDiscount.grid.Users, MODx.grid.Grid, {
	windows: {},

	getMenu: function (grid, rowIndex) {
		var row = grid.getStore().getAt(rowIndex);
		var menu = msDiscount.utils.getMenu(row.data['actions'], this);

		this.addContextMenuItem(menu);
	},

	onClick: function (e) {
		var elem = e.getTarget();
		if (elem.nodeName == 'BUTTON') {
			var row = this.getSelectionModel().getSelected();
			if (typeof(row) != 'undefined') {
				var action = elem.getAttribute('action');
				if (action == 'showMenu') {
					var ri = this.getStore().find('id', row.id);
					return this._showMenu(this, ri, e);
				}
				else if (typeof this[action] === 'function') {
					this.menu.record = row.data;
					return this[action](this, e);
				}
			}
		}
		return this.processEvent('click', e);
	},

	getColumns: function(config) {
		var columns = {
			id: {width: 50},
			name: {width: 200, editor: {xtype: 'textfield'}},
			discount: {editor: {xtype: 'textfield'}},
			joinsum: {editor: {xtype: 'numberfield', allowNegative: false, allowDecimals: true}},
            rates: {editor: {xtype: 'numberfield', allowNegative: false, allowDecimals: true}},
			actions: {width: 50, renderer: msDiscount.utils.renderActions, sortable: false, id: 'actions', header: _('msd_actions')}
		};
		var tmp = [];
		for (var i in columns) {
			if (columns.hasOwnProperty(i)) {
				Ext.applyIf(columns[i], {
					header: _('msd_group_' + i),
					dataIndex: i
				});
				tmp.push(columns[i]);
			}
		}
		return tmp;
	},

	createGroup: function() {
		var createPage = MODx.modx23
			? MODx.action['security/permission']
			: 'security/permission';

		window.open(MODx.config.manager_url + '?a=' + createPage);
	},

	updateGroup: function(btn, e, row) {
		if (typeof(row) != 'undefined') {
			this.menu.record = row.data;
		}
		var id = this.menu.record.id;

		var updatePage = MODx.modx23
			? MODx.action['security/usergroup/update']
			: 'security/usergroup/update';

		window.open(MODx.config.manager_url + '?a=' + updatePage + '&id=' + id);
	},

});
Ext.reg('msd-grid-users', msDiscount.grid.Users);