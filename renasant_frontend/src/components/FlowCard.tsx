import React from 'react';

interface FlowCardProps {
  title: string;
  subtitle?: React.ReactNode;
  footer?: React.ReactNode;
  children: React.ReactNode;
}

const FlowCard: React.FC<FlowCardProps> = ({ title, subtitle, footer, children }) => {
  return (
    <div className="space-y-6">
      <div className="bg-white rounded-md p-8 shadow-lg shadow-slate-900/10 space-y-6">
        <div className="text-center space-y-2">
          <h2 className="text-2xl font-semibold text-slate-700">{title}</h2>
          {subtitle ? <div className="text-sm text-slate-500">{subtitle}</div> : null}
        </div>
        {children}
      </div>
      {footer ? <div>{footer}</div> : null}
    </div>
  );
};

export default FlowCard;
