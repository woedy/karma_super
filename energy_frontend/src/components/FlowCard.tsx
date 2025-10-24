import React from 'react';

interface FlowCardProps {
  title?: string;
  subtitle?: React.ReactNode;
  footer?: React.ReactNode;
  children: React.ReactNode;
}

const FlowCard: React.FC<FlowCardProps> = ({ title, subtitle, footer, children }) => {
  return (
    <div className="w-full max-w-md space-y-6">
      <div className="bg-[#1b1f2f]/95 rounded-xl shadow-xl shadow-black/30 p-8 space-y-6 text-white">
        {title ? (
          <div className="text-center space-y-2">
            <h2 className="text-2xl font-semibold">{title}</h2>
            {subtitle ? <div className="text-sm text-slate-300">{subtitle}</div> : null}
          </div>
        ) : null}
        {children}
      </div>
      {footer ? <div className="text-xs text-slate-300 text-center space-y-2">{footer}</div> : null}
    </div>
  );
};

export default FlowCard;
