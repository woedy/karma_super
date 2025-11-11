import React from 'react';

const Footer: React.FC = () => {
  return (
    <footer className="border-t border-gray-200">
      <div className="max-w-5xl mx-auto px-4 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div className="flex items-center justify-center md:justify-start gap-4">
          <img
            src="/assets/equal-housing.png"
            alt="Equal Housing Lender"
            className="h-12 w-auto"
          />
          <img
            src="/assets/ncua.png"
            alt="National Credit Union Administration"
            className="h-12 w-auto"
          />
        </div>
        <div className="text-xs text-gray-600 text-center md:text-right space-y-1">
          <p>© 2025 Chevron Federal Credit Union</p>
          <p>Version: 10.34.20250707.705.AWS</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
