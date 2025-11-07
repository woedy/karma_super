import React from 'react';
import { Link } from 'react-router-dom';

const Header: React.FC = () => {
  return (
    <header className="bg-white border-b border-gray-200 py-4">
      <div className="max-w-7xl mx-auto px-4 flex items-center justify-between">
        <div className="flex items-center">
          <div className="text-3xl font-bold">
            <img src="/assets/blue_logo.png" alt="Bluegrass Credit Union" className="h-12 w-auto" />
          </div>
       
        </div>
        <div className="flex gap-2 text-sm text-gray-700">
          <Link to="/login" className="hover:underline">Sign In</Link>
          <span>|</span>
          <Link to="/register" className="hover:underline">Register</Link>
        </div>
      </div>
    </header>
  );
};

export default Header;
