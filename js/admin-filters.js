i9idxpressFilters = {

	FillHiddenWithValues: function (checkboxClass, hiddenID) {

		var g = jQuery('.'+checkboxClass), s = '', i;

		for (i = 0; i < g.length; i++) {

			if (g[i].checked) { s == '' ? s = s + g[i].value : s = s + ',' + g[i].value; }

		}

		document.getElementById(hiddenID).value = s;

	},

	FillHiddenWithSelected: function (divID, hiddenID) {

		var f = document.getElementById(divID), g = f.childNodes, s = '', i;

		for (i = 0; i < g.length; i++) {

			if (g[i].selected) { s == '' ? s = s + g[i].value : s = s + ',' + g[i].value; }

		}

		document.getElementById(hiddenID).value = s;

	}

}



jQuery(document).ready(function () {

    jQuery(".i9idxpress-proptype-filter").click(function () { i9idxpressFilters.FillHiddenWithValues('i9idxpress-proptype-filter', 'i9idxpress-RestrictResultsToPropertyType'); });

    jQuery(".i9idxpress-proptype-default").click(function () { i9idxpressFilters.FillHiddenWithValues('i9idxpress-proptype-default', 'i9idxpress-DefaultPropertyType') })

    jQuery(".i9idxpress-statustype-filter").click(function () { i9idxpressFilters.FillHiddenWithValues('i9idxpress-statustype-filter', 'i9idxpress-DefaultListingStatusTypeIDs') })

    jQuery(".i9idxpress-states-filter").click(function () { i9idxpressFilters.FillHiddenWithSelected('i9idxpress-states', 'i9idxpress-RestrictResultsToState'); });

});