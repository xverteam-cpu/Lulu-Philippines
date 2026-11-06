(function () {
  function roundedRect(context, x, y, width, height, radius) {
    context.beginPath();
    if (context.roundRect) {
      context.roundRect(x, y, width, height, radius);
      return;
    }

    context.moveTo(x + radius, y);
    context.lineTo(x + width - radius, y);
    context.quadraticCurveTo(x + width, y, x + width, y + radius);
    context.lineTo(x + width, y + height - radius);
    context.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
    context.lineTo(x + radius, y + height);
    context.quadraticCurveTo(x, y + height, x, y + height - radius);
    context.lineTo(x, y + radius);
    context.quadraticCurveTo(x, y, x + radius, y);
    context.closePath();
  }

  function wrapText(context, text, maxWidth) {
    var words = String(text || '').split(/\s+/);
    var lines = [];
    var line = '';

    words.forEach(function (word) {
      var candidate = line ? line + ' ' + word : word;
      if (line && context.measureText(candidate).width > maxWidth) {
        lines.push(line);
        line = word;
      } else {
        line = candidate;
      }
    });

    if (line) lines.push(line);
    return lines.length ? lines : [''];
  }

  function roundedText(context, text, x, y, maxWidth, lineHeight, align) {
    var lines = wrapText(context, text, maxWidth);
    context.textAlign = align || 'left';
    lines.forEach(function (line, index) {
      context.fillText(line, x, y + index * lineHeight, maxWidth);
    });
    return lines.length;
  }

  function createImage(config) {
    var scale = 2;
    var width = 900;
    var padding = 54;
    var canvas = document.createElement('canvas');
    var context = canvas.getContext('2d');
    var contentWidth = width - padding * 2;
    var rowMetrics = [];
    var rowFont = '400 26px Arial, sans-serif';
    var valueFont = '700 26px Arial, sans-serif';

    context.font = rowFont;
    (config.rows || []).forEach(function (row) {
      var labelLines = wrapText(context, row.label, 320);
      context.font = valueFont;
      var valueLines = wrapText(context, row.value, 365);
      context.font = rowFont;
      rowMetrics.push({
        labelLines: labelLines,
        valueLines: valueLines,
        height: Math.max(labelLines.length, valueLines.length) * 34 + 34
      });
    });

    var rowsHeight = rowMetrics.reduce(function (total, row) {
      return total + row.height;
    }, 0);
    var noteFont = '400 21px Arial, sans-serif';
    context.font = noteFont;
    var noteLines = wrapText(context, config.note, contentWidth);
    var height = 270 + 46 + 150 + 30 + rowsHeight + 42 + noteLines.length * 30 + 54;

    canvas.width = width * scale;
    canvas.height = height * scale;
    context.scale(scale, scale);
    context.fillStyle = '#f2f5f3';
    context.fillRect(0, 0, width, height);

    context.fillStyle = '#075a3b';
    context.font = '700 24px Arial, sans-serif';
    context.textAlign = 'left';
    context.fillText('LuLu Philippines', padding, 62);

    context.fillStyle = '#0a1f17';
    context.font = '700 38px Arial, sans-serif';
    context.fillText(config.title, padding, 122);

    context.font = '700 20px Arial, sans-serif';
    var statusWidth = Math.min(contentWidth, context.measureText(config.status).width + 42);
    roundedRect(context, padding, 148, statusWidth, 42, 21);
    context.fillStyle = config.statusTone === 'confirmed' || config.statusTone === 'approved' ? '#e5f5ec' :
      (config.statusTone === 'rejected' ? '#fff0ee' : '#fff5db');
    context.fill();
    context.fillStyle = config.statusTone === 'confirmed' || config.statusTone === 'approved' ? '#075a3b' :
      (config.statusTone === 'rejected' ? '#982f27' : '#77520a');
    context.fillText(config.status, padding + 20, 176);

    var heroY = 218;
    var heroHeight = 178;
    var gradient = context.createLinearGradient(padding, heroY, width - padding, heroY + heroHeight);
    gradient.addColorStop(0, '#0b6a47');
    gradient.addColorStop(1, '#022418');
    roundedRect(context, padding, heroY, contentWidth, heroHeight, 26);
    context.fillStyle = gradient;
    context.fill();
    context.fillStyle = 'rgba(232,255,244,.76)';
    context.font = '700 19px Arial, sans-serif';
    context.fillText(config.heroLabel.toUpperCase(), padding + 28, heroY + 45);
    context.fillStyle = '#fff';
    context.font = '700 52px Arial, sans-serif';
    context.fillText(config.heroValue, padding + 28, heroY + 110, contentWidth - 56);
    context.fillStyle = 'rgba(232,255,244,.76)';
    context.font = '400 22px Arial, sans-serif';
    context.fillText(config.subtitle, padding + 28, heroY + 148, contentWidth - 56);

    var rowsY = heroY + heroHeight + 28;
    var actualRowsHeight = rowsHeight + 24;
    roundedRect(context, padding, rowsY, contentWidth, actualRowsHeight, 22);
    context.fillStyle = '#fff';
    context.fill();

    var rowY = rowsY + 12;
    rowMetrics.forEach(function (row) {
      if (rowY > rowsY + 12) {
        context.beginPath();
        context.moveTo(padding + 24, rowY);
        context.lineTo(width - padding - 24, rowY);
        context.strokeStyle = '#eef2f0';
        context.lineWidth = 1;
        context.stroke();
      }

      context.font = rowFont;
      context.fillStyle = '#4b5b54';
      row.labelLines.forEach(function (line, index) {
        context.textAlign = 'left';
        context.fillText(line, padding + 24, rowY + 38 + index * 34, 320);
      });
      context.font = valueFont;
      context.fillStyle = '#0a1f17';
      row.valueLines.forEach(function (line, index) {
        context.textAlign = 'right';
        context.fillText(line, width - padding - 24, rowY + 38 + index * 34, 365);
      });
      rowY += row.height;
    });

    context.font = noteFont;
    context.fillStyle = '#708078';
    roundedText(context, config.note, padding + 2, rowsY + actualRowsHeight + 34, contentWidth - 4, 30, 'left');

    context.fillStyle = '#94a39b';
    context.font = '400 17px Arial, sans-serif';
    context.textAlign = 'center';
    context.fillText('Generated from your LuLu account', width / 2, height - 23);

    return canvas.toDataURL('image/png');
  }

  function dataUrlToFile(dataUrl, filename) {
    var parts = dataUrl.split(',');
    var mimeType = parts[0].match(/:(.*?);/)[1];
    var binary = atob(parts[1]);
    var bytes = new Uint8Array(binary.length);

    for (var index = 0; index < binary.length; index += 1) {
      bytes[index] = binary.charCodeAt(index);
    }

    return new File([bytes], filename, { type: mimeType });
  }

  function download(dataUrl, filename) {
    var link = document.createElement('a');
    link.href = dataUrl;
    link.download = filename;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    link.remove();
  }

  window.LuluReceiptImage = {
    share: function (config, filename, statusElement) {
      var image = createImage(config);

      if (typeof File !== 'undefined' && navigator.share) {
        var file = dataUrlToFile(image, filename);
        var shareData = { files: [file], title: config.title, text: config.shareText };
        try {
          if (!navigator.canShare || navigator.canShare(shareData)) {
            navigator.share(shareData).then(function () {
              statusElement.textContent = 'Receipt image shared.';
            }).catch(function (error) {
              if (error.name === 'AbortError') return;

              console.error('Unable to share the receipt image.', error);
              download(image, filename);
              statusElement.textContent = 'Image sharing is unavailable; the receipt image was downloaded.';
            });
            return;
          }
        } catch (error) {
          console.error('Unable to open the receipt share menu.', error);
        }
      }

      download(image, filename);
      statusElement.textContent = 'Image sharing is unavailable; the receipt image was downloaded.';
    },
    download: function (config, filename, statusElement) {
      download(createImage(config), filename);
      statusElement.textContent = 'Receipt image downloaded.';
    }
  };
}());
