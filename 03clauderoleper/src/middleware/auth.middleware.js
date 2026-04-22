const jwt = require('jsonwebtoken');
const User = require('../models/user.model');

// Verify JWT token
const authenticate = async (req, res, next) => {
  try {
    const authHeader = req.headers.authorization;
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      return res.status(401).json({ success: false, message: 'No token provided' });
    }

    const token = authHeader.split(' ')[1];
    const decoded = jwt.verify(token, process.env.JWT_SECRET);

    const user = await User.findById(decoded.id).populate({
      path: 'roles',
      populate: { path: 'permissions' },
    });

    if (!user || !user.isActive) {
      return res.status(401).json({ success: false, message: 'User not found or inactive' });
    }

    req.user = user;
    next();
  } catch (err) {
    return res.status(401).json({ success: false, message: 'Invalid or expired token' });
  }
};

// Check if user has a specific permission
const authorize = (...requiredPermissions) => {
  return (req, res, next) => {
    const userPermissions = new Set();
    req.user.roles.forEach((role) => {
      role.permissions.forEach((perm) => userPermissions.add(perm.name));
    });

    const hasPermission = requiredPermissions.every((perm) => userPermissions.has(perm));

    if (!hasPermission) {
      return res.status(403).json({
        success: false,
        message: `Access denied. Required permissions: ${requiredPermissions.join(', ')}`,
      });
    }
    next();
  };
};

// Check if user has a specific role
const hasRole = (...requiredRoles) => {
  return (req, res, next) => {
    const userRoles = req.user.roles.map((r) => r.name);
    const hasRequiredRole = requiredRoles.some((role) => userRoles.includes(role));

    if (!hasRequiredRole) {
      return res.status(403).json({
        success: false,
        message: `Access denied. Required roles: ${requiredRoles.join(', ')}`,
      });
    }
    next();
  };
};

module.exports = { authenticate, authorize, hasRole };
