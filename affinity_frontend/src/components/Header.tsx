import React from "react";

const Header = () => {
  return (
    <header className="bg-purple-900 text-white py-3 px-6 flex items-center justify-between shadow-md">
      <div className="flex items-center space-x-2">
        <img src="/assets/logo.svg" alt="Affinity Plus Logo" className="h-10 w-auto" />

      </div>
      <div className="flex items-center space-x-3">
        <div className="text-white/80 hover:text-white cursor-pointer transition-colors p-1">
          <i className="fas fa-comments text-xl"></i>
        </div>
      </div>
    </header>
  );
};

export default Header;
