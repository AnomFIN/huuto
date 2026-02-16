# 📋 Quick Chmod Guide for Huuto Deployment

This guide provides a quick reference for setting file permissions after FTP deployment.

## 🚀 Quick Commands (Linux/SSH Access)

If you have SSH access to your server, run these commands:

```bash
cd /home/dajnpsku/public_html/

# Set writable directories
chmod 777 config/
chmod 777 uploads/
chmod 777 storage/logs/
chmod 777 logs/

# Set security files
chmod 644 .htaccess
chmod 644 uploads/.htaccess
chmod 644 logs/.htaccess
```

Or use the automated script:
```bash
cd /home/dajnpsku/public_html/
./set-permissions.sh
```

## 🖱️ FTP Client Setup (FileZilla, WinSCP, cPanel)

### Required Permissions

| Directory/File | Permission | Why |
|----------------|------------|-----|
| `config/` | **777** | Installer writes database.php |
| `uploads/` | **777** | Stores uploaded images |
| `storage/logs/` | **777** | Application error logs |
| `logs/` | **777** | Additional logging |
| `.htaccess` | **644** | Apache security rules |
| `uploads/.htaccess` | **644** | Prevents PHP execution |
| `logs/.htaccess` | **644** | Blocks log access |

### FileZilla Instructions

1. **Connect** to your FTP server (ftp.huuto247.fi)
2. **Navigate** to `/home/dajnpsku/public_html/`
3. **Right-click** on directory → Select **"File Permissions"**
4. **Enter** numeric value:
   - Type `777` for writable directories
   - Type `644` for security files
5. **Click OK**

**Checkbox equivalent:**
- **777** = All 9 boxes checked (Read+Write+Execute for Owner, Group, Public)
- **644** = Owner: Read+Write, Group: Read, Public: Read

### cPanel File Manager Instructions

1. **Log into** cPanel
2. **Open** File Manager
3. **Navigate** to `public_html/`
4. **Select** directory or file
5. **Click** "Permissions" in top menu
6. **Enter** numeric value (777 or 644)
7. **Click** "Change Permissions"

### WinSCP Instructions

1. **Connect** to FTP server
2. **Right-click** directory/file → **Properties**
3. **Go to** Permissions tab
4. **Set** octal value:
   - `777` for writable directories
   - `644` for security files
5. **Click OK**

## 🔢 Permission Numbers Explained

### What does 777 mean?
- **First 7**: Owner can Read (4) + Write (2) + Execute (1) = 7
- **Second 7**: Group can Read (4) + Write (2) + Execute (1) = 7
- **Third 7**: Public can Read (4) + Write (2) + Execute (1) = 7

### What does 644 mean?
- **6**: Owner can Read (4) + Write (2) = 6
- **4**: Group can Read (4) = 4
- **4**: Public can Read (4) = 4

### What does 755 mean?
- **7**: Owner can Read (4) + Write (2) + Execute (1) = 7
- **5**: Group can Read (4) + Execute (1) = 5
- **5**: Public can Read (4) + Execute (1) = 5

## ⚠️ Important Security Notes

### ✅ Safe to use 777:
- `config/` - Only during installation, can be tightened to 755 afterward
- `uploads/` - Protected by .htaccess (no PHP execution)
- `storage/logs/` - Protected by .htaccess
- `logs/` - Protected by .htaccess

### ❌ NEVER use 777 on:
- PHP files (*.php) - Always use 644
- .htaccess files - Always use 644
- Root directory - Use 755

### 🔒 Why .htaccess files are important:
- **Root .htaccess**: Blocks directory listing, protects sensitive files
- **uploads/.htaccess**: Prevents PHP code execution in uploaded files
- **logs/.htaccess**: Blocks direct access to log files

## 🧪 Verification

After setting permissions, verify:

1. **Test installer**: Visit `https://www.huuto247.fi/asennus.php`
   - Should show installation form (if not installed)
   - Should create `config/database.php` successfully

2. **Test uploads**: After installation, try uploading an image
   - Should save to `uploads/` directory
   - Should be accessible via browser

3. **Check logs**: If errors occur, check:
   - `storage/logs/` should contain error logs
   - Logs should be writable

## 🔧 Troubleshooting

### "Permission denied" error
```bash
# Re-apply permissions:
chmod 777 config/ uploads/ storage/logs/ logs/
```

### "Failed to open stream"
- Directory is not writable → Set to 777
- Check parent directory is also accessible (755)

### Installer can't write database.php
- `config/` needs 777 permissions
- Verify config/ directory exists

### Images won't upload
- `uploads/` needs 777 permissions
- Check PHP upload_max_filesize setting

## 📚 Additional Resources

- **Full deployment guide**: See [DEPLOYMENT.md](DEPLOYMENT.md)
- **README**: See [README.md](README.md)
- **Automated script**: Run `./set-permissions.sh` (Linux/SSH only)

## 🆘 Need Help?

If you have SSH access:
```bash
# Check current permissions
ls -la /home/dajnpsku/public_html/

# Run automated setup
cd /home/dajnpsku/public_html/
./set-permissions.sh
```

If using FTP only:
- Use FileZilla/WinSCP/cPanel File Manager
- Follow the instructions above
- See DEPLOYMENT.md for detailed troubleshooting
