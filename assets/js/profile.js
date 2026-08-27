/* BestLife Matrimony - Profile Page JS (Layout 03: Tabbed Sections) */
(function () {
  'use strict';

  /* ── Tab Switching ─────────────────────────────── */
  var tabs = Array.prototype.slice.call(document.querySelectorAll('.tabs-nav .nav-tab'));
  var panels = Array.prototype.slice.call(document.querySelectorAll('.tab-panel'));
  var favLoaded = false;

  function switchToTab(target) {
    tabs.forEach(function (t) { t.classList.remove('active'); });
    panels.forEach(function (p) { p.classList.remove('active'); });

    // Activate the matching tab button
    tabs.forEach(function (t) {
      if (t.getAttribute('data-tab') === target) {
        t.classList.add('active');
      }
    });

    // Activate the matching panel
    var panel = document.getElementById('panel-' + target);
    if (panel) {
      panel.classList.add('active');
    }

    // Persist active tab so it survives a save-&-reload (UX #10)
    try { sessionStorage.setItem('blm_active_tab', target); } catch (e) {}

    if (target === 'favourites' && !favLoaded) {
      favLoaded = true;
      loadFavourites();
    }
  }

  /* ── Favourites ──────────────────────────────── */
  function favCardHTML(p) {
    var age = p.age != null ? p.age : '';
    var h = p.height || '';
    var photo = p.photo ? '<img src="' + esc(p.photo) + '" alt="' + esc(p.name) + '">' : '<div class="fav-avatar-placeholder"><i class="bi bi-person"></i></div>';
    return '<div class="fav-card" onclick="window.location.href=\'./profile_view.php?id=' + p.id + '\'">'
      + '<div class="fav-card-photo">' + photo + '</div>'
      + '<div class="fav-card-info">'
      + '<h6 class="fav-card-name">' + esc(p.name) + (age ? ', ' + age : '') + '</h6>'
      + '<p class="fav-card-meta">' + (h ? esc(h) + ' · ' : '') + esc(p.city) + '</p>'
      + '<p class="fav-card-detail">' + (p.profession ? esc(p.profession) : '') + (p.education ? ' · ' + esc(p.education) : '') + '</p>'
      + '</div></div>';
  }

  function esc(s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }

  function loadFavourites() {
    var ourEl = document.getElementById('ourFavourites');
    var byEl = document.getElementById('favByOthers');
    if (!ourEl || !byEl) return;

    fetch('./profile_favourites_api.php')
      .then(function (r) { return r.json(); })
      .then(function (data) {
        var myFavs = data.my_favourites || [];
        var byOthers = data.favourited_by || [];

        if (myFavs.length) {
          ourEl.innerHTML = myFavs.map(favCardHTML).join('');
        } else {
          ourEl.innerHTML = '<div class="fav-empty"><i class="bi bi-heart"></i><p>You haven\'t added any favourites yet.</p></div>';
        }

        if (byOthers.length) {
          byEl.innerHTML = byOthers.map(favCardHTML).join('');
        } else {
          byEl.innerHTML = '<div class="fav-empty"><i class="bi bi-heart"></i><p>No one has added you to their favourites yet.</p></div>';
        }
      })
      .catch(function () {
        ourEl.innerHTML = '<div class="fav-empty"><i class="bi bi-exclamation-circle"></i><p>Could not load favourites.</p></div>';
        byEl.innerHTML = '<div class="fav-empty"><i class="bi bi-exclamation-circle"></i><p>Could not load favourites.</p></div>';
      });
  }

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      var target = tab.getAttribute('data-tab');
      switchToTab(target);
    });
  });

  // Expose nextTab globally so inline onclick handlers work
  window.nextTab = function (target) {
    switchToTab(target);
  };

  /* ── Delegated click handling (F3) ───────────────
     Replaces inline onclick attributes with data-* attributes so
     behaviour stays in JS instead of being scattered through markup. */
  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-tab-target], [data-remove-main], [data-remove-gallery], [data-cancel-form], [data-toggle-open]');
    if (!t) return;
    if (t.hasAttribute('data-tab-target')) {
      e.preventDefault();
      nextTab(t.getAttribute('data-tab-target'));
    } else if (t.hasAttribute('data-remove-main')) {
      removeMainPhoto(e);
    } else if (t.hasAttribute('data-remove-gallery')) {
      removeGalleryPhoto(e, parseInt(t.getAttribute('data-remove-gallery'), 10) || 0);
    } else if (t.hasAttribute('data-cancel-form')) {
      cancelProfileForm();
    } else if (t.hasAttribute('data-toggle-open')) {
      e.preventDefault();
      var box = t.closest('.missing-fields-box') || t.parentNode;
      box.classList.toggle('open');
      var icon = t.querySelector('i');
      if (icon) { icon.classList.toggle('bi-chevron-down'); icon.classList.toggle('bi-chevron-up'); }
    }
  }, true);

  /* ── Photo Preview - Main ──────────────────────── */
  var ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

  window.previewMainPhoto = function (input) {
    if (input.files && input.files[0]) {
      var file = input.files[0];
      if (file.size > 5 * 1024 * 1024) {
        alert('File is too large. Maximum size is 5MB.');
        input.value = '';
        return;
      }
      if (ALLOWED_TYPES.indexOf(file.type) === -1) {
        alert('Only JPG, PNG or WebP images are allowed.');
        input.value = '';
        return;
      }
      var reader = new FileReader();
      var preview = document.getElementById('mainPhotoPreview');
      var placeholder = document.getElementById('mainPhotoPlaceholder');
      var removeBtn = document.getElementById('mainPhotoRemoveBtn');
      var deleteField = document.getElementById('delete_profile_photo');
      reader.onload = function (e) {
        if (preview) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        }
        if (placeholder) placeholder.style.display = 'none';
        if (removeBtn) removeBtn.style.display = 'flex';
        // Clear any pending delete flag since user just selected a new photo
        if (deleteField) deleteField.value = '0';
        // Update header avatar
        updateHeaderAvatar(e.target.result);
      };
      reader.readAsDataURL(input.files[0]);
    }
  };

  window.removeMainPhoto = function (e) {
    e.stopPropagation();
    e.preventDefault();
    var preview = document.getElementById('mainPhotoPreview');
    var placeholder = document.getElementById('mainPhotoPlaceholder');
    var removeBtn = document.getElementById('mainPhotoRemoveBtn');
    var input = document.getElementById('profile_photo_file');
    var deleteField = document.getElementById('delete_profile_photo');

    if (preview) { preview.src = ''; preview.style.display = 'none'; }
    if (placeholder) placeholder.style.display = '';
    if (removeBtn) removeBtn.style.display = 'none';
    if (input) input.value = '';
    // Set the hidden delete flag so the server knows to remove the photo
    if (deleteField) deleteField.value = '1';
    // Reset header avatar to initial
    resetHeaderAvatar();
  };

  /* ── Photo Preview - Gallery ───────────────────── */
  window.previewGalleryPhoto = function (input, index) {
    if (input.files && input.files[0]) {
      var file = input.files[0];
      if (file.size > 5 * 1024 * 1024) {
        alert('File is too large. Maximum size is 5MB.');
        input.value = '';
        return;
      }
      if (ALLOWED_TYPES.indexOf(file.type) === -1) {
        alert('Only JPG, PNG or WebP images are allowed. Please choose a different file.');
        input.value = '';
        return;
      }
      var reader = new FileReader();
      var preview = document.getElementById('galleryPreview' + index);
      var placeholder = document.getElementById('galleryPlaceholder' + index);
      var removeBtn = document.getElementById('galleryRemoveBtn' + index);
      var deleteField = document.getElementById('delete_gallery_photo_' + index);
      reader.onload = function (e) {
        if (preview) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        }
        if (placeholder) placeholder.style.display = 'none';
        if (removeBtn) removeBtn.style.display = 'flex';
        // Clear any pending delete flag since user just selected a new photo
        if (deleteField) deleteField.value = '0';
      };
      reader.readAsDataURL(input.files[0]);
    }
  };

  window.removeGalleryPhoto = function (e, index) {
    e.stopPropagation();
    e.preventDefault();
    var preview = document.getElementById('galleryPreview' + index);
    var placeholder = document.getElementById('galleryPlaceholder' + index);
    var removeBtn = document.getElementById('galleryRemoveBtn' + index);
    var input = document.getElementById('gallery_file_' + index);
    var deleteField = document.getElementById('delete_gallery_photo_' + index);

    if (preview) { preview.src = ''; preview.style.display = 'none'; }
    if (placeholder) placeholder.style.display = '';
    if (removeBtn) removeBtn.style.display = 'none';
    if (input) input.value = '';
    // Set the hidden delete flag so the server knows to remove the photo
    if (deleteField) deleteField.value = '1';
  };

  /* ── Header Avatar Sync ───────────────────────── */
  function updateHeaderAvatar(src) {
    var avatarImg = document.getElementById('headerAvatarImg');
    var avatarInitial = document.getElementById('headerAvatarInitial');
    if (avatarImg) {
      avatarImg.src = src;
      avatarImg.style.display = 'block';
    }
    if (avatarInitial) {
      avatarInitial.style.display = 'none';
    }
  }

  function resetHeaderAvatar() {
    var avatarImg = document.getElementById('headerAvatarImg');
    var avatarInitial = document.getElementById('headerAvatarInitial');
    if (avatarImg) {
      avatarImg.src = '';
      avatarImg.style.display = 'none';
    }
    if (avatarInitial) {
      avatarInitial.style.display = '';
    }
  }

  /* ── Cancel Form ───────────────────────────────── */
  window.cancelProfileForm = function () {
    if (confirm('Are you sure you want to cancel? Unsaved changes will be lost.')) {
      // Reload preserving the current URL (path + any query string)
      window.location.href = window.location.pathname + window.location.search;
    }
  };

  /* ── Inline validation (UX #9) ─────────────────── */
  function setInvalid(el, msg) {
    var msgEl = el.parentNode.querySelector('.field-error');
    if (!msgEl) {
      msgEl = document.createElement('div');
      msgEl.className = 'field-error';
      msgEl.style.cssText = 'color:#c0392b;font-size:.68rem;margin-top:.15rem;';
      el.parentNode.appendChild(msgEl);
    }
    msgEl.textContent = msg;
    el.classList.add('is-invalid');
    el.setAttribute('aria-invalid', 'true');
  }

  function clearInvalid(el) {
    el.classList.remove('is-invalid');
    el.removeAttribute('aria-invalid');
    var msgEl = el.parentNode.querySelector('.field-error');
    if (msgEl) msgEl.textContent = '';
  }

  function validatePhone(el) {
    var v = (el.value || '').trim();
    if (v === '') { clearInvalid(el); return true; }
    var digits = v.replace(/[^0-9]/g, '');
    var ok = digits.length >= 8 && digits.length <= 15;
    if (!ok) {
      setInvalid(el, 'Please enter a valid phone number (8–15 digits).');
      return false;
    }
    clearInvalid(el);
    return true;
  }

  function validateRequire(el) {
    if (!el.value || !String(el.value).trim()) {
      setInvalid(el, 'This field is required.');
      return false;
    }
    clearInvalid(el);
    return true;
  }

  var phoneEl = document.querySelector('input[name="phone"]');
  if (phoneEl) {
    phoneEl.addEventListener('blur', function () { validatePhone(phoneEl); });
    phoneEl.addEventListener('input', function () { if (phoneEl.value.trim()) clearInvalid(phoneEl); });
  }

  var formEl = document.getElementById('profileForm');
  if (formEl) {
    // Required text fields: phone + full_name (email is disabled/readonly)
    formEl.querySelectorAll('input[name="full_name"], input[name="phone"]').forEach(function (f) {
      f.addEventListener('blur', function () {
        var name = f.getAttribute('name');
        if (name === 'phone') { validatePhone(f); }
        else { validateRequire(f); } // full_name
      });
    });

    /* ── Save loading feedback (UX #1) ─────────── */
    formEl.addEventListener('submit', function () {
      var btn = formEl.querySelector('button[type="submit"]:not(:disabled)');
      if (btn) {
        var label = btn.querySelector('i, span');
        btn.dataset.orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving…';
      }
    });
  }

  /* ── Auto-dismiss success alert ────────────────── */
  var alertEl = document.getElementById('profileAlert');
  if (alertEl) {
    setTimeout(function () {
      alertEl.style.transition = 'opacity 0.4s ease';
      alertEl.style.opacity = '0';
      setTimeout(function () { alertEl.remove(); }, 400);
    }, 4000);
  }

  /* ── Restore active tab after save/reload (UX #10) */
  (function restoreTab() {
    var saved = null;
    try { saved = sessionStorage.getItem('blm_active_tab'); } catch (e) {}
    if (saved) {
      // Only switch if a matching tab+panel exists
      var okTab = tabs.some(function (t) { return t.getAttribute('data-tab') === saved; });
      var okPanel = !!document.getElementById('panel-' + saved);
      if (okTab && okPanel && saved !== 'personal') switchToTab(saved);
    }
  })();

  /* ── Scroll active tab into view on mobile ─────── */
  var activeTab = document.querySelector('.tabs-nav .nav-tab.active');
  if (activeTab) {
    activeTab.scrollIntoView({ inline: 'center', block: 'nearest' });
  }

})();
