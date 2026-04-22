const mongoose = require('mongoose');

const permissionSchema = new mongoose.Schema(
  {
    name: {
      type: String,
      required: [true, 'Permission name is required'],
      unique: true,
      trim: true,
      lowercase: true,
      // e.g. "user:read", "user:write", "post:delete"
    },
    description: {
      type: String,
      trim: true,
    },
    resource: {
      type: String,
      required: [true, 'Resource is required'],
      trim: true,
      lowercase: true,
      // e.g. "user", "post", "product"
    },
    action: {
      type: String,
      required: [true, 'Action is required'],
      enum: ['create', 'read', 'update', 'delete', 'manage'],
      lowercase: true,
    },
  },
  { timestamps: true }
);

// Auto-generate name from resource + action before saving
permissionSchema.pre('validate', function (next) {
  if (this.resource && this.action && !this.name) {
    this.name = `${this.resource}:${this.action}`;
  }
  next();
});

module.exports = mongoose.model('Permission', permissionSchema);
