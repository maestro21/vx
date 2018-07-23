	function addHandler(object, event, handler, useCapture) {
		if (object.addEventListener)
			object.addEventListener(event, handler, useCapture);
		else if (object.attachEvent)
			object.attachEvent('on' + event, handler);
		else object['on' + event] = handler;
	}
	
	// ���������� ��������
	var ua = navigator.userAgent.toLowerCase();
	var isIE = (ua.indexOf("msie") != -1 && ua.indexOf("opera") == -1);
	var isSafari = ua.indexOf("safari") != -1;
	var isGecko = (ua.indexOf("gecko") != -1 && !isSafari);
	
	// ��������� �����������
	if (isIE || isSafari) addHandler (document, "keydown", hotSave);
	else addHandler (document, "keypress", hotSave);
	
	function hotSave(evt) {
		// �������� ������ event
		evt = evt || window.event;
		var key = evt.keyCode || evt.which;
		// ���������� ������� Ctrl+S
		key = !isGecko ? (key == 83 ? 1 : 0) : (key == 115 ? 1 : 0);
		if (evt.ctrlKey && key) {
			// ��������� ��������� ������� � ����������
			if(evt.preventDefault) evt.preventDefault();
			evt.returnValue = false;
			// ��������� ����� �������, �� �������
			saveFn();
			// ���������� ����� � ����
			window.focus();
			return false;
		}
	}
