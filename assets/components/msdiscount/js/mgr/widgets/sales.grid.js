msDiscount.grid.Sales = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		layout: 'anchor',
		url: msDiscount.config.connector_url,
		baseParams: {
			action: 'mgr/sales/getlist'
		},
		fields: ['id', 'discount', 'name', 'description', 'begins', 'ends', 'active', 'image', 'actions'],
		autoHeight: true,
		paging: true,
		remoteSort: true,
		columns: this.getColumns(config),
		sm: new Ext.grid.CheckboxSelectionModel(),
		tbar: this.getTopBar(config),
		viewConfig: {
			forceFit: true,
			enableRowBody: true,
			autoFill: true,
			showPreview: true,
			scrollOffset: 0,
			getRowClass: function (rec, ri, p) {
				var cls = [];
				if (!rec.data.active) {
					cls.push('msd-row-disabled');
				}
				return cls.join(' ');
			}
		},
		listeners: {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.updateSale(grid, e, row);
			}
		}
	});
	msDiscount.grid.Sales.superclass.constructor.call(this,config);
};
Ext.extend(msDiscount.grid.Sales,MODx.grid.Grid,{
	windows: {},

	getMenu: function (grid, rowIndex) {
		var ids = this._getSelectedIds();

		var row = grid.getStore().getAt(rowIndex);
		var menu = msDiscount.utils.getMenu(row.data['actions'], this, ids);

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

	getTopBar: function(config) {
		return [{
			text: (MODx.modx23
				? '<i class="icon icon-plus"></i> '
				: '<i class="fa fa-plus"></i> ')
			+ _('msd_btn_sale_create'),
			handler: this.createSale,
			scope: this
		}];
	},

	getColumns: function(config) {
		var columns = {
			id: {width: 35},
			name: {width: 100},
			discount: {width: 75},
			description: {hidden: true},
			begins: {width: 75, renderer: miniShop2.utils.formatDate},
			ends: {width: 75, renderer: miniShop2.utils.formatDate},
			active: {width: 50, renderer: msDiscount.utils.renderBoolean/*, editor: {xtype: 'combo-boolean', renderer:'boolean'}*/},
			image: {width: 75, renderer: this.renderThumb, sortable: false},
			actions: {width: 75, renderer: msDiscount.utils.renderActions, sortable: false, id: 'actions', header: _('msd_actions')}
		};
		var tmp = [];
		for (var i in columns) {
			if (columns.hasOwnProperty(i)) {
				Ext.applyIf( columns[i], {
					header: _('msd_sales_' +  i),
					dataIndex: i,
					sortable: true,
				});
				tmp.push(columns[i]);
			}
		}
		return tmp;
	},

	renderThumb: function(value) {
		if (value) {
			return '<img src="/' + value + '" height="41" style="display:block;margin:auto;"/>';
		}
		else {
			return '';
		}
	},

	createSale: function(btn,e) {
		var id = 'msd-window-sale-create';
		var w = Ext.getCmp(id);
		if (w) {return;}

		w = MODx.load({
			id: id,
			xtype: 'msd-window-sale',
			record: {id: 0},
			mode: 'create',
			listeners: {
				success: {fn:function(response) {
					this.refresh();
					if (response.a.result.object) {
						this.updateSale('', '', {data: response.a.result.object}, 1);
					}
				}, scope:this},
				hide: function(item) {
					window.setTimeout(function() {
						item.close();
					}, 100);
				}
			}
		});
		w.show(e.target);
	},

	updateSale: function(btn,e,row, tab) {
		if (typeof(row) != 'undefined') {this.menu.record = row.data;}
		if (!tab) {tab = 0;}
		var id = this.menu.record.id;

		MODx.Ajax.request({
			url: msDiscount.config.connector_url,
			params: {
				action: 'mgr/sales/get',
				id: id
			},
			listeners: {
				success: {fn:function(r) {
					var w = MODx.load({
						xtype: 'msd-window-sale',
						record: r.object,
						mode: 'update',
						listeners: {
							success: {fn: function() {
								this.refresh();
							}, scope: this},
							afterrender: {fn:function() {
								if (tab != 0) {
									Ext.getCmp(this.config.id + '-tabs').setActiveTab(tab);
								}
							}},
						}
					});
					w.fp.getForm().reset();
					w.fp.getForm().setValues(r.object);
					w.show(e.target);
				},scope:this}
			}
		});
	},

	saleAction: function(method) {
		var ids = this._getSelectedIds();
		if (!ids.length) {
			return false;
		}
		MODx.Ajax.request({
			url: msDiscount.config.connector_url,
			params: {
				action: 'mgr/sales/multiple',
				method: method,
				ids: Ext.util.JSON.encode(ids),
			},
			listeners: {
				success: {
					fn: function () {
						this.refresh();
					}, scope: this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message);
					}, scope: this
				},
			}
		})
	},

	enableSale: function(btn,e) {
		this.saleAction('enable');
	},

	disableSale: function(btn,e) {
		this.saleAction('disable');
	},

	removeSale: function(btn,e) {
		Ext.MessageBox.confirm(
			_('msd_action_remove'),
			_('msd_action_remove_confirm'),
			function(val) {
				if (val == 'yes') {
					this.saleAction('remove');
				}
			},
			this
		);
	},

	_getSelectedIds: function() {
		var ids = [];
		var selected = this.getSelectionModel().getSelections();

		for (var i in selected) {
			if (!selected.hasOwnProperty(i)) {
				continue;
			}
			ids.push(selected[i]['id']);
		}

		return ids;
	},

});
Ext.reg('msd-grid-sales', msDiscount.grid.Sales);


msDiscount.window.Sale = function(config) {
	config = config || {};
	if (!config.id) {
		config.id = Ext.id();
	}

	Ext.applyIf(config, {
		id: config.id,
		title: _('msd_sales_' + (config['mode'] || 'create'))
			+ (config['mode'] == 'update' ? '"' + config.record['name'] + '"' : ''),
		autoHeight: true,
		width: 650,
		url: msDiscount.config.connector_url,
		action: 'mgr/sales/' + (config['mode'] || 'create'),
		fields: this.getTabs(config),
		keys: this.getKeys(config)
	});
	msDiscount.window.Sale.superclass.constructor.call(this,config);
};
Ext.extend(msDiscount.window.Sale, MODx.Window, {

	getTabs: function(config) {
		return [{
			id: config.id + '-tabs',
			xtype: 'modx-tabs',
			bodyStyle: MODx.modx23 ? '' : 'padding: 5px;',
			defaults: { border: false, autoHeight: true },
			border: true,
			activeTab: 0,
			autoHeight: true,
			stateful: false,
			stateId: 'msd-window-sales-' + config['mode'],
			stateEvents: ['tabchange'],
			getState:function() {return {activeTab: this.items.indexOf(this.getActiveTab())};},
			items: [{
				title: _('msd_sales_main'),
				layout: 'form',
				items: this.getMainFields(config)
			},{
				title: _('msd_users'),
				disabled: config['mode'] == 'create',
				layout: 'anchor',
				items: {
					xtype: 'msd-grid-sales-group',
					record: config.record,
					type: 'users',
					sale_id: config.record.id
				}
			},{
				title: _('msd_products'),
				disabled: config['mode'] == 'create',
				layout: 'anchor',
				items: {
					xtype: 'msd-grid-sales-group',
					record: config.record,
					type: 'products',
					sale_id: config.record.id
				}
			}]
		}];
	},

	getMainFields: function(config) {
		return [
			{xtype: 'hidden', name: 'id'},
			{xtype: 'textfield', name: 'name', fieldLabel: _('msd_sales_name'), anchor: '100%'},
			{
				layout: 'column',
				border: true,
				anchor: '100%',
				style: {margin: '10px 0 0 0'},
				items: [{
					columnWidth: .5,
					layout: 'form',
					items: [
						{xtype: 'minishop2-xdatetime', name: 'begins', fieldLabel: _('msd_sales_begins'), anchor: '99%'},
						{xtype: 'textfield', name: 'discount', fieldLabel: _('msd_sales_discount'), anchor: '50%', value: 0},
						{xtype: 'minishop2-combo-resource', name: 'resource', fieldLabel: _('msd_sales_resource'), anchor: '99%'}
					]
				},{
					columnWidth: .5,
					layout: 'form',
					style: {margin: 0},
					items: [
						{xtype: 'minishop2-xdatetime', name: 'ends', fieldLabel: _('msd_sales_ends'), anchor: '99%'},
						{xtype: 'combo-boolean', name: 'active', fieldLabel: _('msd_sales_active'), anchor: '50%', hiddenName: 'active', value: true},
						{xtype: 'minishop2-combo-browser', name: 'image', fieldLabel: _('msd_sales_image'), anchor: '100%'}
					]
				}]
			},
			{xtype: 'textarea', name: 'description', fieldLabel: _('msd_sales_description'), anchor: '100%', height: 75}
		];
	},

	getKeys: function() {
		return [{
			key: Ext.EventObject.ENTER,
			shift: true,
			fn: function() {
				this.submit()
			}, scope: this
		}];
	},

	loadDropZones: function() {},

});
Ext.reg('msd-window-sale', msDiscount.window.Sale);


msDiscount.grid.SalesMemberGroup = function(config) {
	config = config || {};
	if (!config.id) {
		config.id = Ext.id();
	}
	Ext.applyIf(config, {
		layout: 'anchor',
		id: config.id,
		url: msDiscount.config.connector_url,
		baseParams: {
			action: 'mgr/sales/members/getlist',
			type: config.type,
			sale_id: config.record.id
		},
		fields: ['sale_id', 'group_id', 'type', 'relation', 'name', 'discount', 'actions'],
		autoHeight: true,
		paging: true,
		remoteSort: true,
		pageSize: 5,
		sm: new Ext.grid.CheckboxSelectionModel(),
		tbar: this.getTopBar(config),
		columns: this.getColumns(config),
	});
	msDiscount.grid.SalesMemberGroup.superclass.constructor.call(this, config);
};
Ext.extend(msDiscount.grid.SalesMemberGroup, MODx.grid.Grid, {

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

	getTopBar: function(config) {
		return [{
			xtype: 'msd-combo-group',
			id: config.id + '-combo',
			type: config.type,
			sale_id: config.record.id,
			listeners: {
				select: {fn: this.addMember, scope: this}
			}
		}];
	},

	getColumns: function() {
		var columns = {
			name: {width: 75},
			relation: {width: 50, header: _('msd_members_relation'), renderer: this._renderRelation},
			discount: {width: 50},
			actions: {width: 50, renderer: msDiscount.utils.renderActions, sortable: false, id: 'actions', header: _('msd_actions')}
		};
		var tmp = [];
		for (var i in columns) {
			if (columns.hasOwnProperty(i)) {
				Ext.applyIf( columns[i], {
					header: _('msd_group_' +  i),
					dataIndex: i,
					sortable: true,
				});
				tmp.push(columns[i]);
			}
		}
		return tmp;
	},

	addMember: function(combo, row) {
		combo.reset();

		MODx.Ajax.request({
			url: this.config.url,
			params: {
				action: 'mgr/sales/members/create',
				sale_id: this.config.record.id,
				group_id: row.id,
				type: this.config.type
			},
			listeners: {
				success: {
					fn: function(r) {
						combo.getStore().reload();
						this.refresh();
					}, scope: this
				}
			}
		});
	},

	memberAction: function(method) {
		var ids = this._getSelectedIds();
		if (!ids.length) {
			return false;
		}
		MODx.Ajax.request({
			url: msDiscount.config.connector_url,
			params: {
				action: 'mgr/sales/members/multiple',
				method: method,
				ids: Ext.util.JSON.encode(ids),
			},
			listeners: {
				success: {
					fn: function() {
						this.refresh();
						Ext.getCmp(this.config.id + '-combo').getStore().reload();
					}, scope: this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message);
					}, scope: this
				},
			}
		})
	},

	switchMember: function() {
		this.memberAction('switch');
	},

	removeMember: function() {
		this.memberAction('remove');
	},

	_renderRelation: function(value) {
		if (value == 'in') {
			return '<span style="color:green;">' + _('msd_members_relation_in') + '</span>';
		}
		else if (value == 'out') {
			return '<span style="color:red;">' + _('msd_members_relation_out') + '</span>';
		}
		else {
			return value;
		}
	},

	_getSelectedIds: function() {
		var ids = [];
		var selected = this.getSelectionModel().getSelections();

		for (var i in selected) {
			if (!selected.hasOwnProperty(i)) {
				continue;
			}
			ids.push({
				sale_id: selected[i]['data']['sale_id'],
				group_id: selected[i]['data']['group_id'],
				type: selected[i]['data']['type'],
			});
		}

		return ids;
	},

});
Ext.reg('msd-grid-sales-group', msDiscount.grid.SalesMemberGroup);